// Parcours de bout en bout dans un vrai Chromium, piloté par le protocole DevTools (CDP).
// Aucune dépendance npm : Node 22+ et un Chrome ou Chromium installé.
//   node tests/e2e/browser.mjs                       # contre http://127.0.0.1:8087
//   DEMO_URL=https://guilloche.memolabs.dev node tests/e2e/browser.mjs
//   CHROME_BIN=/usr/bin/chromium node tests/e2e/browser.mjs
import { spawn } from 'node:child_process';
import { existsSync, mkdirSync, mkdtempSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import assert from 'node:assert/strict';

const base = process.env.DEMO_URL || 'http://127.0.0.1:8087';
const out = 'var/e2e';
const chromeBin = process.env.CHROME_BIN || [
    '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
    '/Applications/Chromium.app/Contents/MacOS/Chromium',
    '/usr/bin/chromium',
    '/usr/bin/chromium-browser',
    '/usr/bin/google-chrome',
].find(existsSync);
assert.ok(chromeBin, 'Chrome introuvable : définir CHROME_BIN');

const chrome = spawn(chromeBin, ['--headless=new', '--remote-debugging-port=0', '--no-first-run', '--no-default-browser-check', `--user-data-dir=${mkdtempSync(join(tmpdir(), 'guilloche-e2e-'))}`, 'about:blank'], { stdio: ['ignore', 'ignore', 'pipe'] });
const endpoint = await new Promise((resolve, reject) => {
    let buffer = '';
    chrome.stderr.on('data', chunk => {
        buffer += chunk;
        const match = buffer.match(/DevTools listening on (ws:\/\/\S+)/);
        if (match) resolve(match[1]);
    });
    chrome.on('exit', code => reject(new Error(`Chrome s'est arrêté (${code})`)));
});
const socket = new WebSocket(endpoint);
await new Promise(resolve => socket.addEventListener('open', resolve, {once:true}));
let seq=0;
const pending = new Map();
const listeners = new Map();
socket.addEventListener('message', event => {
    const data=JSON.parse(event.data);
    if (data.id) {
        const call=pending.get(data.id); pending.delete(data.id);
        if (data.error) call.reject(new Error(JSON.stringify(data.error))); else call.resolve(data.result);
    } else for (const fn of listeners.get(data.method)||[]) fn(data.params);
});
const send = (method, params={}, sessionId) => new Promise((resolve,reject) => {
    const id=++seq; pending.set(id,{resolve,reject}); socket.send(JSON.stringify({id,method,params,sessionId}));
});
const {targetId} = await send('Target.createTarget',{url:'about:blank'});
const {sessionId} = await send('Target.attachToTarget',{targetId,flatten:true});
const cmd = (method, params={}) => send(method,params,sessionId);
const js = async expression => {
    const result=await cmd('Runtime.evaluate',{expression,returnByValue:true,awaitPromise:true});
    if (result.exceptionDetails) throw new Error(JSON.stringify(result.exceptionDetails));
    return result.result.value;
};
const delay=ms=>new Promise(resolve=>setTimeout(resolve,ms));
const until=async (expression) => {
    for(let i=0;i<150;i++) { if(await js(expression)) return; await delay(40); }
    throw new Error('Timeout: '+expression+' state='+await js('JSON.stringify({url:location.href,ready:document.readyState,frame:document.querySelector("turbo-frame")?.outerHTML.slice(0,300),feedback:document.querySelector(".results-feedback")?.textContent,complete:document.querySelector("turbo-frame")?.complete,constructor:document.querySelector("turbo-frame")?.constructor.name})')); 
};
const go=async path=>{await cmd('Page.navigate',{url:base+path});await until('document.readyState === "complete"');};
const settled=()=>until('document.querySelector("#mission-results")?.hasAttribute("complete") && document.querySelector(".results-feedback")?.textContent === ""');
const check=(value,message)=>{assert.ok(value,message);console.log('PASS '+message);};
const result=[];
const record=(name,detail)=>result.push({name,detail});
await cmd('Page.enable'); await cmd('Page.bringToFront'); await cmd('Runtime.enable'); await cmd('Network.enable');
const errors=[];
let stage='initial';
listeners.set('Runtime.exceptionThrown',[event=>errors.push({stage,text:event.exceptionDetails.text,description:event.exceptionDetails.exception?.description})]);
mkdirSync(out,{recursive:true});
try {
    for(const width of [390,768,1280,1440]) {
        await cmd('Emulation.setDeviceMetricsOverride',{width,height:900,deviceScaleFactor:1,mobile:false});
        for(const selected of [false,true]) {
            await go('/missions'+(selected?'?selected=1':''));
            const geometry=await js(`JSON.stringify({width:innerWidth,scroll:document.documentElement.scrollWidth,reader:getComputedStyle(document.querySelector('.mission-workspace__reader')).display,list:getComputedStyle(document.querySelector('.mission-workspace__list')).display,readerWidth:document.querySelector('.mission-workspace__reader').getBoundingClientRect().width})`);
            const g=JSON.parse(geometry);
            check(g.scroll<=width,`no overflow at ${width}, selected=${selected}`);
            check(width>=1280?(g.list!=='none'&&g.reader!=='none'&&g.readerWidth>=360):(selected?g.list==='none':g.reader==='none'),`master/detail layout at ${width}, selected=${selected}`);
            const shot=await cmd('Page.captureScreenshot',{format:'png',captureBeyondViewport:true});
            writeFileSync(`var/e2e/missions-${width}-${selected?'detail':'list'}.png`,Buffer.from(shot.data,'base64'));
            record(`viewport ${width} selected=${selected}`,g);
        }
    }
    await cmd('Emulation.setDeviceMetricsOverride',{width:390,height:844,deviceScaleFactor:1,mobile:false});
    await go('/missions');
    const hover=await js('(() => {const r=document.querySelector(".mission-list__link").getBoundingClientRect();return {x:r.x+r.width/2,y:r.y+r.height/2}})()');
    await cmd('Input.dispatchMouseEvent',{type:'mouseMoved',...hover});
    await delay(250);
    check(await js('document.querySelector(".results-feedback").textContent === "" && document.querySelector(".results-error").hidden'),'prefetch hover does not announce a navigation');
    await js(`document.querySelector('#mission-search').value='rémunération';document.querySelector('#mission-status').value='action';document.querySelector('form').requestSubmit()`);
    await settled();
    check(await js('document.querySelectorAll(".mission-list__item").length === 1 && location.search.includes("status=action")'),'GET filters update URL and list');
    await js('document.querySelector(".mission-list__link").focus()');
    await cmd('Input.dispatchKeyEvent',{type:'keyDown',key:'Enter',code:'Enter',windowsVirtualKeyCode:13});
    await cmd('Input.dispatchKeyEvent',{type:'keyUp',key:'Enter',code:'Enter',windowsVirtualKeyCode:13});
    await settled(); await until('document.activeElement.classList.contains("mission-sheet__title")');
    check(await js('location.search.includes("selected=1")'),'keyboard selection and focus');
    // Régression « clignotement au clic » : un rendu Turbo ne rejoue pas les entrées des éléments inchangés.
    check(await js('document.getAnimations().filter(a => ["row-in","fade-in"].includes(a.animationName) && a.effect.target.closest(".mission-list")).length === 0'),'frame render does not replay list entrances');
    check(await js('[...document.querySelectorAll(".mission-list .ui-guilloche__trace")].every(p => getComputedStyle(p).animationName === "none")'),'list rosettes are not retraced on each render');
    check(await js('document.querySelector(".mission-sheet").classList.contains("is-entering")'),'newly opened mission sheet prints once');

    const {currentIndex,entries}=await cmd('Page.getNavigationHistory');
    await cmd('Page.navigateToHistoryEntry',{entryId:entries[currentIndex-1].id});
    await until('!location.search.includes("selected") && !document.querySelector(".mission-sheet__title")');
    check(await js('document.querySelector("#mission-search").value === "rémunération"'),'history restores filters and deselection');
    await js('document.querySelector(".mission-list__link").click()'); await settled();
    await js('document.querySelector(".mission-sheet__next a").click()');
    await until('location.pathname === "/documents/1" && document.body.textContent.includes("Exemple fictif")');
    check(await js('document.querySelector("h1").textContent.includes("Synthèse")'),'mission action opens associated document');
    await go('/missions?status=done&selected=1');
    check(await js('!location.search.includes("selected") && document.querySelectorAll(".mission-list__item").length === 1'),'stale selection canonical URL');
    await go('/missions?q=introuvable');
    check(await js('document.body.textContent.includes("Aucune mission trouvée")'),'empty filter state');
    await go('/missions?selected=1');
    await js('document.querySelector("#mission-status").value="action";document.querySelector("form").requestSubmit()');
    await settled();
    check(await js('location.search.includes("status=action") && location.search.includes("selected=1") && !document.querySelector(".mission-sheet").classList.contains("is-entering")'),'unchanged selection is not reprinted after a filter');
    // Fail only a frame selection request, leaving the current page available.
    for(const kind of ['network','http','missing']) {
        stage=kind;
        await go('/missions');
        await cmd('Fetch.enable',{patterns:[{urlPattern:'*selected=1*',requestStage:'Request'}]});
        listeners.set('Fetch.requestPaused',[async event=>{
            if(kind==='network') await cmd('Fetch.failRequest',{requestId:event.requestId,errorReason:'Failed'});
            else await cmd('Fetch.fulfillRequest',{requestId:event.requestId,responseCode:kind==='http'?503:200,responseHeaders:[{name:'Content-Type',value:'text/html'}],body:Buffer.from('<html><body>Unavailable</body></html>').toString('base64')});
        }]);
        await js('document.querySelector(".mission-list__link").click()');
        await until('!document.querySelector(".results-error").hidden');
        check(await js('document.querySelectorAll(".mission-list__item").length === 5'),`${kind} failure keeps previous results and retry`);
        await cmd('Fetch.disable'); listeners.delete('Fetch.requestPaused');
        await js('document.querySelector(".results-error button").click()');
        await until('document.querySelector(".mission-sheet__title")?.textContent.includes("Rémunération")');
        check(await js('document.querySelector(".results-error").hidden'),`${kind} retry recovers selection`);
    }
    await go('/missions');
    await cmd('Network.emulateNetworkConditions',{offline:false,latency:600,downloadThroughput:-1,uploadThroughput:-1});
    await js('document.querySelector(".mission-list__link").click()');
    await until('document.querySelector(".results-feedback").textContent.includes("Chargement")');
    check(await js('document.querySelectorAll(".mission-list__item").length === 5'),'loading feedback keeps occupied region stable');
    await settled();
    await cmd('Network.emulateNetworkConditions',{offline:false,latency:0,downloadThroughput:-1,uploadThroughput:-1});
    await cmd('Emulation.setEmulatedMedia',{features:[{name:'prefers-reduced-motion',value:'reduce'}]});
    check(await js('[...document.querySelectorAll(".ui-guilloche__trace")].every(p => getComputedStyle(p).animationName === "none") && [...document.querySelectorAll(".mission-sheet.is-entering")].every(e => getComputedStyle(e).animationName === "fade-in")'),'reduced motion removes the trace and the sheet print');
    stage='documents';
    for(const width of [390,768,1280,1440]) {
        await cmd('Emulation.setDeviceMetricsOverride',{width,height:900,deviceScaleFactor:1,mobile:false});
        await go('/documents?mission=2');
        check(await js('document.documentElement.scrollWidth <= innerWidth'),`documents no overflow at ${width}`);
        if(width===390) check(await js('document.querySelector("#document-mission").getBoundingClientRect().width >= 300'),'mobile mission filter stays readable');
        check(await js('document.querySelectorAll(".document-item--library").length === 2 && document.querySelector("#document-mission").value === "2"'),`documents mission filter retained at ${width}`);
        const shot=await cmd('Page.captureScreenshot',{format:'png',captureBeyondViewport:true});
        writeFileSync(`var/e2e/documents-${width}.png`,Buffer.from(shot.data,'base64'));
        record(`documents viewport ${width}`,{overflow:false,mission:2,documents:2});
    }
    await js('document.querySelector("#document-search").value="lettre";document.querySelector("form").requestSubmit()');
    await until('document.querySelector("#document-results").hasAttribute("complete") && document.querySelector(".results-feedback").textContent === ""');
    check(await js('document.querySelectorAll(".document-item--library").length === 1 && document.activeElement.dataset.filteredResultsTarget === "summary"'),'documents filter GET and focus summary');
    await js('document.querySelector(".document-item__link").focus()');
    await cmd('Input.dispatchKeyEvent',{type:'keyDown',key:'Enter',code:'Enter',windowsVirtualKeyCode:13});
    await cmd('Input.dispatchKeyEvent',{type:'keyUp',key:'Enter',code:'Enter',windowsVirtualKeyCode:13});
    await until('location.pathname === "/documents/2" && document.body.textContent.includes("Exemple fictif")');
    check(await js('document.querySelector(".page-back").href.includes("mission=2") && document.querySelector(".page-back").href.includes("q=lettre")'),'document explicit return preserves library filters');
    await js('document.querySelector(".page-back").click()');
    await until('location.pathname === "/documents" && document.querySelectorAll(".document-item--library").length === 1');
    const docHistory=await cmd('Page.getNavigationHistory');
    await cmd('Page.navigateToHistoryEntry',{entryId:docHistory.entries[docHistory.currentIndex-1].id});
    await until('location.pathname === "/documents/2"');
    check(await js('document.body.textContent.includes("Lettre de mission")'),'document browser history returns to reading');
    await go('/documents?mission=5');
    check(await js('document.body.textContent.includes("Aucun document trouvé")'),'documents empty mission state');
    for(const kind of ['network','http','missing']) {
        stage='documents-'+kind;
        await go('/documents');
        await cmd('Fetch.enable',{patterns:[{urlPattern:'*mission=2*',requestStage:'Request'}]});
        listeners.set('Fetch.requestPaused',[async event=>{
            if(kind==='network') await cmd('Fetch.failRequest',{requestId:event.requestId,errorReason:'Failed'});
            else await cmd('Fetch.fulfillRequest',{requestId:event.requestId,responseCode:kind==='http'?503:200,responseHeaders:[{name:'Content-Type',value:'text/html'}],body:Buffer.from('<html><body>Unavailable</body></html>').toString('base64')});
        }]);
        await js('document.querySelector("#document-mission").value="2";document.querySelector("form").requestSubmit()');
        await until('!document.querySelector(".results-error").hidden');
        check(await js('document.querySelectorAll(".document-item--library").length === 5'),`documents ${kind} failure retains previous list`);
        await cmd('Fetch.disable'); listeners.delete('Fetch.requestPaused');
        await js('document.querySelector(".results-error button").click()');
        await until('location.search.includes("mission=2") && document.querySelectorAll(".document-item--library").length === 2');
        check(await js('document.querySelector(".results-error").hidden'),`documents ${kind} retry recovers filters`);
    }
    stage='system';
    await cmd('Emulation.setEmulatedMedia',{features:[{name:'prefers-reduced-motion',value:'no-preference'}]});
    for(const width of [320,390,768,1280,1440]) {
        await cmd('Emulation.setDeviceMetricsOverride',{width,height:1000,deviceScaleFactor:1,mobile:false});
        await go('/design-system');
        check(await js('document.documentElement.scrollWidth <= innerWidth'),`system no overflow at ${width}`);
        const shot=await cmd('Page.captureScreenshot',{format:'png',captureBeyondViewport:true});
        writeFileSync(`var/e2e/system-light-${width}.png`,Buffer.from(shot.data,'base64'));
        record(`system viewport ${width}`,{overflow:false});
    }
    check(await js('getComputedStyle(document.querySelector("[data-contract=merged]")).fontSize === "14px" && getComputedStyle(document.querySelector("h1")).fontFamily.includes("Bodoni")'),'documented typography matches rendered controls and title');
    await js('document.querySelector(".topbar__theme").click()');
    // Le thème s'applique dans le rappel d'une View Transition : asynchrone.
    await until('document.documentElement.dataset.theme === "dark" && document.querySelector(".topbar__theme").getAttribute("aria-pressed") === "true"');
    check(await js('getComputedStyle(document.body).backgroundColor !== "rgb(237, 241, 234)"'),'dark theme changes semantic roles');
    const darkShot=await cmd('Page.captureScreenshot',{format:'png',captureBeyondViewport:true});
    writeFileSync('var/e2e/system-dark-1440.png',Buffer.from(darkShot.data,'base64'));
    await go('/design-system?section=feedback');
    check(await js('document.documentElement.dataset.theme === "dark"'),'theme persists through navigation');
    check(await js('getComputedStyle(document.querySelector("[data-contract=badge-red-solid]")).backgroundColor !== getComputedStyle(document.querySelector("[data-contract=badge-blue-solid]")).backgroundColor'),'solid badge tones stay semantically distinct');
    await js('document.querySelector("[data-action$=openDialog]").focus();document.querySelector("[data-action$=openDialog]").click()');
    await until('document.querySelector(".ui-dialog").open');
    check(await js('document.querySelector(".ui-dialog").contains(document.activeElement)'),'native dialog owns keyboard focus');
    await cmd('Input.dispatchKeyEvent',{type:'keyDown',key:'Escape',code:'Escape',windowsVirtualKeyCode:27});
    await cmd('Input.dispatchKeyEvent',{type:'keyUp',key:'Escape',code:'Escape',windowsVirtualKeyCode:27});
    await until('!document.querySelector(".ui-dialog").open');
    check(await js('document.activeElement.dataset.action === "catalog#openDialog"'),'dialog Escape returns focus');
    await js('document.querySelector("[data-action$=openDialog]").click()');
    await until('document.querySelector(".ui-dialog").open');
    await js('document.querySelector(".ui-dialog form button").click()');
    await until('!document.querySelector(".ui-dialog").open');
    check(true,'dialog close control and reopening work');
    await js('document.querySelector("[data-action$=openDialog]").click();document.querySelector(".ui-dialog form button").click();document.querySelector(".ui-dialog").dispatchEvent(new MouseEvent("click",{bubbles:true,clientX:0,clientY:0}));document.querySelector("[data-action$=openDialog]").click()');
    await delay(200);
    check(await js('document.querySelector(".ui-dialog").open'),'stale close animation does not close reopened dialog');
    await js('document.querySelector(".ui-dialog form button").click()');
    await until('!document.querySelector(".ui-dialog").open');
    await go('/design-system?section=motion');
    const stateHeight=await js('document.querySelector(".state-lab__canvas").getBoundingClientRect().height');
    await js('document.querySelector("[data-catalog-state-param=loading]").click()');
    check(await js('!document.querySelector("[data-state=loading]").hidden'),'system skeleton preview');
    check(await js('document.querySelector(".state-lab__canvas").getBoundingClientRect().height')===stateHeight,'state preview keeps its height while loading');
    await js('document.querySelector("[data-action$=reduceMotion]").click()');
    check(await js('getComputedStyle(document.querySelector(".ui-skeleton__mark")).animationName === "none"'),'system motion control stops the hatch animation');
    await js('document.querySelector(".topbar__theme").click()');
    await go('/missions');
    await cmd('Network.emulateNetworkConditions',{offline:false,latency:600,downloadThroughput:-1,uploadThroughput:-1});
    await js('document.querySelector(".mission-list__link").focus();document.querySelector(".mission-list__link").click()');
    await until('!document.querySelector(".results-region__mask").hidden');
    check(await js('document.querySelector(".results-feedback").textContent.includes("Chargement")'),'product uses real delayed skeleton');
    await until('document.querySelector(".results-region__mask").hidden && document.activeElement.classList.contains("mission-sheet__title")');
    check(true,'product skeleton clears and selected title receives focus');
    await cmd('Network.emulateNetworkConditions',{offline:false,latency:0,downloadThroughput:-1,uploadThroughput:-1});
    stage='nojs';
    await cmd('Emulation.setScriptExecutionDisabled',{value:true});
    await go('/missions?status=action');
    await js('document.querySelector("#mission-search").value="rémunération";document.querySelector("#mission-search").focus()');
    await cmd('Input.dispatchKeyEvent',{type:'keyDown',key:'Enter',code:'Enter',windowsVirtualKeyCode:13});
    await cmd('Input.dispatchKeyEvent',{type:'char',text:'\r',unmodifiedText:'\r',key:'Enter',code:'Enter',windowsVirtualKeyCode:13});
    await cmd('Input.dispatchKeyEvent',{type:'keyUp',key:'Enter',code:'Enter',windowsVirtualKeyCode:13});
    await until('location.search.includes("q=") && document.querySelectorAll(".mission-list__item").length === 1');
    check(await js('document.querySelectorAll(".mission-list__item").length === 1 && document.querySelector(".filter-clear").hidden'),'GET filter submitted without JS');
    await go('/missions?status=action');
    check(await js('document.querySelectorAll(".mission-list__item").length === 2 && document.querySelector(".filter-clear").hidden'),'server page available without JS');
    await js('document.querySelector(".mission-list__link").focus()');
    await cmd('Input.dispatchKeyEvent',{type:'keyDown',key:'Enter',code:'Enter',windowsVirtualKeyCode:13});
    await cmd('Input.dispatchKeyEvent',{type:'keyUp',key:'Enter',code:'Enter',windowsVirtualKeyCode:13});
    await until('document.querySelector(".mission-sheet__title")?.textContent.includes("Rémunération")');
    check(await js('getComputedStyle(document.querySelector(".mission-workspace__reader")).display !== "none"'),'detail works without JS');
    await js('document.querySelector(".mission-sheet__next a").focus()');
    await cmd('Input.dispatchKeyEvent',{type:'keyDown',key:'Enter',code:'Enter',windowsVirtualKeyCode:13});
    await cmd('Input.dispatchKeyEvent',{type:'keyUp',key:'Enter',code:'Enter',windowsVirtualKeyCode:13});
    await until('location.pathname === "/documents/1"');
    check(await js('document.body.textContent.includes("Exemple fictif")'),'document readable without JS');
    await go('/documents?mission=2');
    check(await js('document.querySelectorAll(".document-item--library").length === 2'),'documents readable without JS');
    await js('document.querySelector("#document-search").value="lettre";document.querySelector("#document-search").focus()');
    await cmd('Input.dispatchKeyEvent',{type:'keyDown',key:'Enter',code:'Enter',windowsVirtualKeyCode:13});
    await cmd('Input.dispatchKeyEvent',{type:'char',text:'\r',unmodifiedText:'\r',key:'Enter',code:'Enter',windowsVirtualKeyCode:13});
    await cmd('Input.dispatchKeyEvent',{type:'keyUp',key:'Enter',code:'Enter',windowsVirtualKeyCode:13});
    await until('location.search.includes("q=lettre") && document.querySelectorAll(".document-item--library").length === 1');
    check(true,'documents GET filter without JS');
    await cmd('Emulation.setScriptExecutionDisabled',{value:false});
    check(errors.length===0,'no uncaught browser exceptions: '+JSON.stringify(errors));
    writeFileSync(`${out}/evidence.json`,JSON.stringify({checked_at:new Date().toISOString(),base,result,errors,status:'PASS'},null,2)+'\n');
} finally {
    socket.close();
    chrome.kill();
}
