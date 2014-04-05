// jQuery - v1.3.2
(function(){var l=this,g,y=l.jQuery,p=l.$,o=l.jQuery=l.$=function(E,F){return new o.fn.init(E,F)},D=/^[^<]*(<(.|\s)+>)[^>]*$|^#([\w-]+)$/,f=/^.[^:#\[\.,]*$/;o.fn=o.prototype={init:function(E,H){E=E||document;if(E.nodeType){this[0]=E;this.length=1;this.context=E;return this}if(typeof E==="string"){var G=D.exec(E);if(G&&(G[1]||!H)){if(G[1]){E=o.clean([G[1]],H)}else{var I=document.getElementById(G[3]);if(I&&I.id!=G[3]){return o().find(E)}var F=o(I||[]);F.context=document;F.selector=E;return F}}else{return o(H).find(E)}}else{if(o.isFunction(E)){return o(document).ready(E)}}if(E.selector&&E.context){this.selector=E.selector;this.context=E.context}return this.setArray(o.isArray(E)?E:o.makeArray(E))},selector:"",jquery:"1.3.2",size:function(){return this.length},get:function(E){return E===g?Array.prototype.slice.call(this):this[E]},pushStack:function(F,H,E){var G=o(F);G.prevObject=this;G.context=this.context;if(H==="find"){G.selector=this.selector+(this.selector?" ":"")+E}else{if(H){G.selector=this.selector+"."+H+"("+E+")"}}return G},setArray:function(E){this.length=0;Array.prototype.push.apply(this,E);return this},each:function(F,E){return o.each(this,F,E)},index:function(E){return o.inArray(E&&E.jquery?E[0]:E,this)},attr:function(F,H,G){var E=F;if(typeof F==="string"){if(H===g){return this[0]&&o[G||"attr"](this[0],F)}else{E={};E[F]=H}}return this.each(function(I){for(F in E){o.attr(G?this.style:this,F,o.prop(this,E[F],G,I,F))}})},css:function(E,F){if((E=="width"||E=="height")&&parseFloat(F)<0){F=g}return this.attr(E,F,"curCSS")},text:function(F){if(typeof F!=="object"&&F!=null){return this.empty().append((this[0]&&this[0].ownerDocument||document).createTextNode(F))}var E="";o.each(F||this,function(){o.each(this.childNodes,function(){if(this.nodeType!=8){E+=this.nodeType!=1?this.nodeValue:o.fn.text([this])}})});return E},wrapAll:function(E){if(this[0]){var F=o(E,this[0].ownerDocument).clone();if(this[0].parentNode){F.insertBefore(this[0])}F.map(function(){var G=this;while(G.firstChild){G=G.firstChild}return G}).append(this)}return this},wrapInner:function(E){return this.each(function(){o(this).contents().wrapAll(E)})},wrap:function(E){return this.each(function(){o(this).wrapAll(E)})},append:function(){return this.domManip(arguments,true,function(E){if(this.nodeType==1){this.appendChild(E)}})},prepend:function(){return this.domManip(arguments,true,function(E){if(this.nodeType==1){this.insertBefore(E,this.firstChild)}})},before:function(){return this.domManip(arguments,false,function(E){this.parentNode.insertBefore(E,this)})},after:function(){return this.domManip(arguments,false,function(E){this.parentNode.insertBefore(E,this.nextSibling)})},end:function(){return this.prevObject||o([])},push:[].push,sort:[].sort,splice:[].splice,find:function(E){if(this.length===1){var F=this.pushStack([],"find",E);F.length=0;o.find(E,this[0],F);return F}else{return this.pushStack(o.unique(o.map(this,function(G){return o.find(E,G)})),"find",E)}},clone:function(G){var E=this.map(function(){if(!o.support.noCloneEvent&&!o.isXMLDoc(this)){var I=this.outerHTML;if(!I){var J=this.ownerDocument.createElement("div");J.appendChild(this.cloneNode(true));I=J.innerHTML}return o.clean([I.replace(/ jQuery\d+="(?:\d+|null)"/g,"").replace(/^\s*/,"")])[0]}else{return this.cloneNode(true)}});if(G===true){var H=this.find("*").andSelf(),F=0;E.find("*").andSelf().each(function(){if(this.nodeName!==H[F].nodeName){return}var I=o.data(H[F],"events");for(var K in I){for(var J in I[K]){o.event.add(this,K,I[K][J],I[K][J].data)}}F++})}return E},filter:function(E){return this.pushStack(o.isFunction(E)&&o.grep(this,function(G,F){return E.call(G,F)})||o.multiFilter(E,o.grep(this,function(F){return F.nodeType===1})),"filter",E)},closest:function(E){var G=o.expr.match.POS.test(E)?o(E):null,F=0;return this.map(function(){var H=this;while(H&&H.ownerDocument){if(G?G.index(H)>-1:o(H).is(E)){o.data(H,"closest",F);return H}H=H.parentNode;F++}})},not:function(E){if(typeof E==="string"){if(f.test(E)){return this.pushStack(o.multiFilter(E,this,true),"not",E)}else{E=o.multiFilter(E,this)}}var F=E.length&&E[E.length-1]!==g&&!E.nodeType;return this.filter(function(){return F?o.inArray(this,E)<0:this!=E})},add:function(E){return this.pushStack(o.unique(o.merge(this.get(),typeof E==="string"?o(E):o.makeArray(E))))},is:function(E){return !!E&&o.multiFilter(E,this).length>0},hasClass:function(E){return !!E&&this.is("."+E)},val:function(K){if(K===g){var E=this[0];if(E){if(o.nodeName(E,"option")){return(E.attributes.value||{}).specified?E.value:E.text}if(o.nodeName(E,"select")){var I=E.selectedIndex,L=[],M=E.options,H=E.type=="select-one";if(I<0){return null}for(var F=H?I:0,J=H?I+1:M.length;F<J;F++){var G=M[F];if(G.selected){K=o(G).val();if(H){return K}L.push(K)}}return L}return(E.value||"").replace(/\r/g,"")}return g}if(typeof K==="number"){K+=""}return this.each(function(){if(this.nodeType!=1){return}if(o.isArray(K)&&/radio|checkbox/.test(this.type)){this.checked=(o.inArray(this.value,K)>=0||o.inArray(this.name,K)>=0)}else{if(o.nodeName(this,"select")){var N=o.makeArray(K);o("option",this).each(function(){this.selected=(o.inArray(this.value,N)>=0||o.inArray(this.text,N)>=0)});if(!N.length){this.selectedIndex=-1}}else{this.value=K}}})},html:function(E){return E===g?(this[0]?this[0].innerHTML.replace(/ jQuery\d+="(?:\d+|null)"/g,""):null):this.empty().append(E)},replaceWith:function(E){return this.after(E).remove()},eq:function(E){return this.slice(E,+E+1)},slice:function(){return this.pushStack(Array.prototype.slice.apply(this,arguments),"slice",Array.prototype.slice.call(arguments).join(","))},map:function(E){return this.pushStack(o.map(this,function(G,F){return E.call(G,F,G)}))},andSelf:function(){return this.add(this.prevObject)},domManip:function(J,M,L){if(this[0]){var I=(this[0].ownerDocument||this[0]).createDocumentFragment(),F=o.clean(J,(this[0].ownerDocument||this[0]),I),H=I.firstChild;if(H){for(var G=0,E=this.length;G<E;G++){L.call(K(this[G],H),this.length>1||G>0?I.cloneNode(true):I)}}if(F){o.each(F,z)}}return this;function K(N,O){return M&&o.nodeName(N,"table")&&o.nodeName(O,"tr")?(N.getElementsByTagName("tbody")[0]||N.appendChild(N.ownerDocument.createElement("tbody"))):N}}};o.fn.init.prototype=o.fn;function z(E,F){if(F.src){o.ajax({url:F.src,async:false,dataType:"script"})}else{o.globalEval(F.text||F.textContent||F.innerHTML||"")}if(F.parentNode){F.parentNode.removeChild(F)}}function e(){return +new Date}o.extend=o.fn.extend=function(){var J=arguments[0]||{},H=1,I=arguments.length,E=false,G;if(typeof J==="boolean"){E=J;J=arguments[1]||{};H=2}if(typeof J!=="object"&&!o.isFunction(J)){J={}}if(I==H){J=this;--H}for(;H<I;H++){if((G=arguments[H])!=null){for(var F in G){var K=J[F],L=G[F];if(J===L){continue}if(E&&L&&typeof L==="object"&&!L.nodeType){J[F]=o.extend(E,K||(L.length!=null?[]:{}),L)}else{if(L!==g){J[F]=L}}}}}return J};var b=/z-?index|font-?weight|opacity|zoom|line-?height/i,q=document.defaultView||{},s=Object.prototype.toString;o.extend({noConflict:function(E){l.$=p;if(E){l.jQuery=y}return o},isFunction:function(E){return s.call(E)==="[object Function]"},isArray:function(E){return s.call(E)==="[object Array]"},isXMLDoc:function(E){return E.nodeType===9&&E.documentElement.nodeName!=="HTML"||!!E.ownerDocument&&o.isXMLDoc(E.ownerDocument)},globalEval:function(G){if(G&&/\S/.test(G)){var F=document.getElementsByTagName("head")[0]||document.documentElement,E=document.createElement("script");E.type="text/javascript";if(o.support.scriptEval){E.appendChild(document.createTextNode(G))}else{E.text=G}F.insertBefore(E,F.firstChild);F.removeChild(E)}},nodeName:function(F,E){return F.nodeName&&F.nodeName.toUpperCase()==E.toUpperCase()},each:function(G,K,F){var E,H=0,I=G.length;if(F){if(I===g){for(E in G){if(K.apply(G[E],F)===false){break}}}else{for(;H<I;){if(K.apply(G[H++],F)===false){break}}}}else{if(I===g){for(E in G){if(K.call(G[E],E,G[E])===false){break}}}else{for(var J=G[0];H<I&&K.call(J,H,J)!==false;J=G[++H]){}}}return G},prop:function(H,I,G,F,E){if(o.isFunction(I)){I=I.call(H,F)}return typeof I==="number"&&G=="curCSS"&&!b.test(E)?I+"px":I},className:{add:function(E,F){o.each((F||"").split(/\s+/),function(G,H){if(E.nodeType==1&&!o.className.has(E.className,H)){E.className+=(E.className?" ":"")+H}})},remove:function(E,F){if(E.nodeType==1){E.className=F!==g?o.grep(E.className.split(/\s+/),function(G){return !o.className.has(F,G)}).join(" "):""}},has:function(F,E){return F&&o.inArray(E,(F.className||F).toString().split(/\s+/))>-1}},swap:function(H,G,I){var E={};for(var F in G){E[F]=H.style[F];H.style[F]=G[F]}I.call(H);for(var F in G){H.style[F]=E[F]}},css:function(H,F,J,E){if(F=="width"||F=="height"){var L,G={position:"absolute",visibility:"hidden",display:"block"},K=F=="width"?["Left","Right"]:["Top","Bottom"];function I(){L=F=="width"?H.offsetWidth:H.offsetHeight;if(E==="border"){return}o.each(K,function(){if(!E){L-=parseFloat(o.curCSS(H,"padding"+this,true))||0}if(E==="margin"){L+=parseFloat(o.curCSS(H,"margin"+this,true))||0}else{L-=parseFloat(o.curCSS(H,"border"+this+"Width",true))||0}})}if(H.offsetWidth!==0){I()}else{o.swap(H,G,I)}return Math.max(0,Math.round(L))}return o.curCSS(H,F,J)},curCSS:function(I,F,G){var L,E=I.style;if(F=="opacity"&&!o.support.opacity){L=o.attr(E,"opacity");return L==""?"1":L}if(F.match(/float/i)){F=w}if(!G&&E&&E[F]){L=E[F]}else{if(q.getComputedStyle){if(F.match(/float/i)){F="float"}F=F.replace(/([A-Z])/g,"-$1").toLowerCase();var M=q.getComputedStyle(I,null);if(M){L=M.getPropertyValue(F)}if(F=="opacity"&&L==""){L="1"}}else{if(I.currentStyle){var J=F.replace(/\-(\w)/g,function(N,O){return O.toUpperCase()});L=I.currentStyle[F]||I.currentStyle[J];if(!/^\d+(px)?$/i.test(L)&&/^\d/.test(L)){var H=E.left,K=I.runtimeStyle.left;I.runtimeStyle.left=I.currentStyle.left;E.left=L||0;L=E.pixelLeft+"px";E.left=H;I.runtimeStyle.left=K}}}}return L},clean:function(F,K,I){K=K||document;if(typeof K.createElement==="undefined"){K=K.ownerDocument||K[0]&&K[0].ownerDocument||document}if(!I&&F.length===1&&typeof F[0]==="string"){var H=/^<(\w+)\s*\/?>$/.exec(F[0]);if(H){return[K.createElement(H[1])]}}var G=[],E=[],L=K.createElement("div");o.each(F,function(P,S){if(typeof S==="number"){S+=""}if(!S){return}if(typeof S==="string"){S=S.replace(/(<(\w+)[^>]*?)\/>/g,function(U,V,T){return T.match(/^(abbr|br|col|img|input|link|meta|param|hr|area|embed)$/i)?U:V+"></"+T+">"});var O=S.replace(/^\s+/,"").substring(0,10).toLowerCase();var Q=!O.indexOf("<opt")&&[1,"<select multiple='multiple'>","</select>"]||!O.indexOf("<leg")&&[1,"<fieldset>","</fieldset>"]||O.match(/^<(thead|tbody|tfoot|colg|cap)/)&&[1,"<table>","</table>"]||!O.indexOf("<tr")&&[2,"<table><tbody>","</tbody></table>"]||(!O.indexOf("<td")||!O.indexOf("<th"))&&[3,"<table><tbody><tr>","</tr></tbody></table>"]||!O.indexOf("<col")&&[2,"<table><tbody></tbody><colgroup>","</colgroup></table>"]||!o.support.htmlSerialize&&[1,"div<div>","</div>"]||[0,"",""];L.innerHTML=Q[1]+S+Q[2];while(Q[0]--){L=L.lastChild}if(!o.support.tbody){var R=/<tbody/i.test(S),N=!O.indexOf("<table")&&!R?L.firstChild&&L.firstChild.childNodes:Q[1]=="<table>"&&!R?L.childNodes:[];for(var M=N.length-1;M>=0;--M){if(o.nodeName(N[M],"tbody")&&!N[M].childNodes.length){N[M].parentNode.removeChild(N[M])}}}if(!o.support.leadingWhitespace&&/^\s/.test(S)){L.insertBefore(K.createTextNode(S.match(/^\s*/)[0]),L.firstChild)}S=o.makeArray(L.childNodes)}if(S.nodeType){G.push(S)}else{G=o.merge(G,S)}});if(I){for(var J=0;G[J];J++){if(o.nodeName(G[J],"script")&&(!G[J].type||G[J].type.toLowerCase()==="text/javascript")){E.push(G[J].parentNode?G[J].parentNode.removeChild(G[J]):G[J])}else{if(G[J].nodeType===1){G.splice.apply(G,[J+1,0].concat(o.makeArray(G[J].getElementsByTagName("script"))))}I.appendChild(G[J])}}return E}return G},attr:function(J,G,K){if(!J||J.nodeType==3||J.nodeType==8){return g}var H=!o.isXMLDoc(J),L=K!==g;G=H&&o.props[G]||G;if(J.tagName){var F=/href|src|style/.test(G);if(G=="selected"&&J.parentNode){J.parentNode.selectedIndex}if(G in J&&H&&!F){if(L){if(G=="type"&&o.nodeName(J,"input")&&J.parentNode){throw"type property can't be changed"}J[G]=K}if(o.nodeName(J,"form")&&J.getAttributeNode(G)){return J.getAttributeNode(G).nodeValue}if(G=="tabIndex"){var I=J.getAttributeNode("tabIndex");return I&&I.specified?I.value:J.nodeName.match(/(button|input|object|select|textarea)/i)?0:J.nodeName.match(/^(a|area)$/i)&&J.href?0:g}return J[G]}if(!o.support.style&&H&&G=="style"){return o.attr(J.style,"cssText",K)}if(L){J.setAttribute(G,""+K)}var E=!o.support.hrefNormalized&&H&&F?J.getAttribute(G,2):J.getAttribute(G);return E===null?g:E}if(!o.support.opacity&&G=="opacity"){if(L){J.zoom=1;J.filter=(J.filter||"").replace(/alpha\([^)]*\)/,"")+(parseInt(K)+""=="NaN"?"":"alpha(opacity="+K*100+")")}return J.filter&&J.filter.indexOf("opacity=")>=0?(parseFloat(J.filter.match(/opacity=([^)]*)/)[1])/100)+"":""}G=G.replace(/-([a-z])/ig,function(M,N){return N.toUpperCase()});if(L){J[G]=K}return J[G]},trim:function(E){return(E||"").replace(/^\s+|\s+$/g,"")},makeArray:function(G){var E=[];if(G!=null){var F=G.length;if(F==null||typeof G==="string"||o.isFunction(G)||G.setInterval){E[0]=G}else{while(F){E[--F]=G[F]}}}return E},inArray:function(G,H){for(var E=0,F=H.length;E<F;E++){if(H[E]===G){return E}}return -1},merge:function(H,E){var F=0,G,I=H.length;if(!o.support.getAll){while((G=E[F++])!=null){if(G.nodeType!=8){H[I++]=G}}}else{while((G=E[F++])!=null){H[I++]=G}}return H},unique:function(K){var F=[],E={};try{for(var G=0,H=K.length;G<H;G++){var J=o.data(K[G]);if(!E[J]){E[J]=true;F.push(K[G])}}}catch(I){F=K}return F},grep:function(F,J,E){var G=[];for(var H=0,I=F.length;H<I;H++){if(!E!=!J(F[H],H)){G.push(F[H])}}return G},map:function(E,J){var F=[];for(var G=0,H=E.length;G<H;G++){var I=J(E[G],G);if(I!=null){F[F.length]=I}}return F.concat.apply([],F)}});var C=navigator.userAgent.toLowerCase();o.browser={version:(C.match(/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/)||[0,"0"])[1],safari:/webkit/.test(C),opera:/opera/.test(C),msie:/msie/.test(C)&&!/opera/.test(C),mozilla:/mozilla/.test(C)&&!/(compatible|webkit)/.test(C)};o.each({parent:function(E){return E.parentNode},parents:function(E){return o.dir(E,"parentNode")},next:function(E){return o.nth(E,2,"nextSibling")},prev:function(E){return o.nth(E,2,"previousSibling")},nextAll:function(E){return o.dir(E,"nextSibling")},prevAll:function(E){return o.dir(E,"previousSibling")},siblings:function(E){return o.sibling(E.parentNode.firstChild,E)},children:function(E){return o.sibling(E.firstChild)},contents:function(E){return o.nodeName(E,"iframe")?E.contentDocument||E.contentWindow.document:o.makeArray(E.childNodes)}},function(E,F){o.fn[E]=function(G){var H=o.map(this,F);if(G&&typeof G=="string"){H=o.multiFilter(G,H)}return this.pushStack(o.unique(H),E,G)}});o.each({appendTo:"append",prependTo:"prepend",insertBefore:"before",insertAfter:"after",replaceAll:"replaceWith"},function(E,F){o.fn[E]=function(G){var J=[],L=o(G);for(var K=0,H=L.length;K<H;K++){var I=(K>0?this.clone(true):this).get();o.fn[F].apply(o(L[K]),I);J=J.concat(I)}return this.pushStack(J,E,G)}});o.each({removeAttr:function(E){o.attr(this,E,"");if(this.nodeType==1){this.removeAttribute(E)}},addClass:function(E){o.className.add(this,E)},removeClass:function(E){o.className.remove(this,E)},toggleClass:function(F,E){if(typeof E!=="boolean"){E=!o.className.has(this,F)}o.className[E?"add":"remove"](this,F)},remove:function(E){if(!E||o.filter(E,[this]).length){o("*",this).add([this]).each(function(){o.event.remove(this);o.removeData(this)});if(this.parentNode){this.parentNode.removeChild(this)}}},empty:function(){o(this).children().remove();while(this.firstChild){this.removeChild(this.firstChild)}}},function(E,F){o.fn[E]=function(){return this.each(F,arguments)}});function j(E,F){return E[0]&&parseInt(o.curCSS(E[0],F,true),10)||0}var h="jQuery"+e(),v=0,A={};o.extend({cache:{},data:function(F,E,G){F=F==l?A:F;var H=F[h];if(!H){H=F[h]=++v}if(E&&!o.cache[H]){o.cache[H]={}}if(G!==g){o.cache[H][E]=G}return E?o.cache[H][E]:H},removeData:function(F,E){F=F==l?A:F;var H=F[h];if(E){if(o.cache[H]){delete o.cache[H][E];E="";for(E in o.cache[H]){break}if(!E){o.removeData(F)}}}else{try{delete F[h]}catch(G){if(F.removeAttribute){F.removeAttribute(h)}}delete o.cache[H]}},queue:function(F,E,H){if(F){E=(E||"fx")+"queue";var G=o.data(F,E);if(!G||o.isArray(H)){G=o.data(F,E,o.makeArray(H))}else{if(H){G.push(H)}}}return G},dequeue:function(H,G){var E=o.queue(H,G),F=E.shift();if(!G||G==="fx"){F=E[0]}if(F!==g){F.call(H)}}});o.fn.extend({data:function(E,G){var H=E.split(".");H[1]=H[1]?"."+H[1]:"";if(G===g){var F=this.triggerHandler("getData"+H[1]+"!",[H[0]]);if(F===g&&this.length){F=o.data(this[0],E)}return F===g&&H[1]?this.data(H[0]):F}else{return this.trigger("setData"+H[1]+"!",[H[0],G]).each(function(){o.data(this,E,G)})}},removeData:function(E){return this.each(function(){o.removeData(this,E)})},queue:function(E,F){if(typeof E!=="string"){F=E;E="fx"}if(F===g){return o.queue(this[0],E)}return this.each(function(){var G=o.queue(this,E,F);if(E=="fx"&&G.length==1){G[0].call(this)}})},dequeue:function(E){return this.each(function(){o.dequeue(this,E)})}});
// Sizzle CSS Selector Engine - v0.9.3
(function(){var R=/((?:\((?:\([^()]+\)|[^()]+)+\)|\[(?:\[[^[\]]*\]|['"][^'"]*['"]|[^[\]'"]+)+\]|\\.|[^ >+~,(\[\\]+)+|[>+~])(\s*,\s*)?/g,L=0,H=Object.prototype.toString;var F=function(Y,U,ab,ac){ab=ab||[];U=U||document;if(U.nodeType!==1&&U.nodeType!==9){return[]}if(!Y||typeof Y!=="string"){return ab}var Z=[],W,af,ai,T,ad,V,X=true;R.lastIndex=0;while((W=R.exec(Y))!==null){Z.push(W[1]);if(W[2]){V=RegExp.rightContext;break}}if(Z.length>1&&M.exec(Y)){if(Z.length===2&&I.relative[Z[0]]){af=J(Z[0]+Z[1],U)}else{af=I.relative[Z[0]]?[U]:F(Z.shift(),U);while(Z.length){Y=Z.shift();if(I.relative[Y]){Y+=Z.shift()}af=J(Y,af)}}}else{var ae=ac?{expr:Z.pop(),set:E(ac)}:F.find(Z.pop(),Z.length===1&&U.parentNode?U.parentNode:U,Q(U));af=F.filter(ae.expr,ae.set);if(Z.length>0){ai=E(af)}else{X=false}while(Z.length){var ah=Z.pop(),ag=ah;if(!I.relative[ah]){ah=""}else{ag=Z.pop()}if(ag==null){ag=U}I.relative[ah](ai,ag,Q(U))}}if(!ai){ai=af}if(!ai){throw"Syntax error, unrecognized expression: "+(ah||Y)}if(H.call(ai)==="[object Array]"){if(!X){ab.push.apply(ab,ai)}else{if(U.nodeType===1){for(var aa=0;ai[aa]!=null;aa++){if(ai[aa]&&(ai[aa]===true||ai[aa].nodeType===1&&K(U,ai[aa]))){ab.push(af[aa])}}}else{for(var aa=0;ai[aa]!=null;aa++){if(ai[aa]&&ai[aa].nodeType===1){ab.push(af[aa])}}}}}else{E(ai,ab)}if(V){F(V,U,ab,ac);if(G){hasDuplicate=false;ab.sort(G);if(hasDuplicate){for(var aa=1;aa<ab.length;aa++){if(ab[aa]===ab[aa-1]){ab.splice(aa--,1)}}}}}return ab};F.matches=function(T,U){return F(T,null,null,U)};F.find=function(aa,T,ab){var Z,X;if(!aa){return[]}for(var W=0,V=I.order.length;W<V;W++){var Y=I.order[W],X;if((X=I.match[Y].exec(aa))){var U=RegExp.leftContext;if(U.substr(U.length-1)!=="\\"){X[1]=(X[1]||"").replace(/\\/g,"");Z=I.find[Y](X,T,ab);if(Z!=null){aa=aa.replace(I.match[Y],"");break}}}}if(!Z){Z=T.getElementsByTagName("*")}return{set:Z,expr:aa}};F.filter=function(ad,ac,ag,W){var V=ad,ai=[],aa=ac,Y,T,Z=ac&&ac[0]&&Q(ac[0]);while(ad&&ac.length){for(var ab in I.filter){if((Y=I.match[ab].exec(ad))!=null){var U=I.filter[ab],ah,af;T=false;if(aa==ai){ai=[]}if(I.preFilter[ab]){Y=I.preFilter[ab](Y,aa,ag,ai,W,Z);if(!Y){T=ah=true}else{if(Y===true){continue}}}if(Y){for(var X=0;(af=aa[X])!=null;X++){if(af){ah=U(af,Y,X,aa);var ae=W^!!ah;if(ag&&ah!=null){if(ae){T=true}else{aa[X]=false}}else{if(ae){ai.push(af);T=true}}}}}if(ah!==g){if(!ag){aa=ai}ad=ad.replace(I.match[ab],"");if(!T){return[]}break}}}if(ad==V){if(T==null){throw"Syntax error, unrecognized expression: "+ad}else{break}}V=ad}return aa};var I=F.selectors={order:["ID","NAME","TAG"],match:{ID:/#((?:[\w\u00c0-\uFFFF_-]|\\.)+)/,CLASS:/\.((?:[\w\u00c0-\uFFFF_-]|\\.)+)/,NAME:/\[name=['"]*((?:[\w\u00c0-\uFFFF_-]|\\.)+)['"]*\]/,ATTR:/\[\s*((?:[\w\u00c0-\uFFFF_-]|\\.)+)\s*(?:(\S?=)\s*(['"]*)(.*?)\3|)\s*\]/,TAG:/^((?:[\w\u00c0-\uFFFF\*_-]|\\.)+)/,CHILD:/:(only|nth|last|first)-child(?:\((even|odd|[\dn+-]*)\))?/,POS:/:(nth|eq|gt|lt|first|last|even|odd)(?:\((\d*)\))?(?=[^-]|$)/,PSEUDO:/:((?:[\w\u00c0-\uFFFF_-]|\\.)+)(?:\((['"]*)((?:\([^\)]+\)|[^\2\(\)]*)+)\2\))?/},attrMap:{"class":"className","for":"htmlFor"},attrHandle:{href:function(T){return T.getAttribute("href")}},relative:{"+":function(aa,T,Z){var X=typeof T==="string",ab=X&&!/\W/.test(T),Y=X&&!ab;if(ab&&!Z){T=T.toUpperCase()}for(var W=0,V=aa.length,U;W<V;W++){if((U=aa[W])){while((U=U.previousSibling)&&U.nodeType!==1){}aa[W]=Y||U&&U.nodeName===T?U||false:U===T}}if(Y){F.filter(T,aa,true)}},">":function(Z,U,aa){var X=typeof U==="string";if(X&&!/\W/.test(U)){U=aa?U:U.toUpperCase();for(var V=0,T=Z.length;V<T;V++){var Y=Z[V];if(Y){var W=Y.parentNode;Z[V]=W.nodeName===U?W:false}}}else{for(var V=0,T=Z.length;V<T;V++){var Y=Z[V];if(Y){Z[V]=X?Y.parentNode:Y.parentNode===U}}if(X){F.filter(U,Z,true)}}},"":function(W,U,Y){var V=L++,T=S;if(!U.match(/\W/)){var X=U=Y?U:U.toUpperCase();T=P}T("parentNode",U,V,W,X,Y)},"~":function(W,U,Y){var V=L++,T=S;if(typeof U==="string"&&!U.match(/\W/)){var X=U=Y?U:U.toUpperCase();T=P}T("previousSibling",U,V,W,X,Y)}},find:{ID:function(U,V,W){if(typeof V.getElementById!=="undefined"&&!W){var T=V.getElementById(U[1]);return T?[T]:[]}},NAME:function(V,Y,Z){if(typeof Y.getElementsByName!=="undefined"){var U=[],X=Y.getElementsByName(V[1]);for(var W=0,T=X.length;W<T;W++){if(X[W].getAttribute("name")===V[1]){U.push(X[W])}}return U.length===0?null:U}},TAG:function(T,U){return U.getElementsByTagName(T[1])}},preFilter:{CLASS:function(W,U,V,T,Z,aa){W=" "+W[1].replace(/\\/g,"")+" ";if(aa){return W}for(var X=0,Y;(Y=U[X])!=null;X++){if(Y){if(Z^(Y.className&&(" "+Y.className+" ").indexOf(W)>=0)){if(!V){T.push(Y)}}else{if(V){U[X]=false}}}}return false},ID:function(T){return T[1].replace(/\\/g,"")},TAG:function(U,T){for(var V=0;T[V]===false;V++){}return T[V]&&Q(T[V])?U[1]:U[1].toUpperCase()},CHILD:function(T){if(T[1]=="nth"){var U=/(-?)(\d*)n((?:\+|-)?\d*)/.exec(T[2]=="even"&&"2n"||T[2]=="odd"&&"2n+1"||!/\D/.test(T[2])&&"0n+"+T[2]||T[2]);T[2]=(U[1]+(U[2]||1))-0;T[3]=U[3]-0}T[0]=L++;return T},ATTR:function(X,U,V,T,Y,Z){var W=X[1].replace(/\\/g,"");if(!Z&&I.attrMap[W]){X[1]=I.attrMap[W]}if(X[2]==="~="){X[4]=" "+X[4]+" "}return X},PSEUDO:function(X,U,V,T,Y){if(X[1]==="not"){if(X[3].match(R).length>1||/^\w/.test(X[3])){X[3]=F(X[3],null,null,U)}else{var W=F.filter(X[3],U,V,true^Y);if(!V){T.push.apply(T,W)}return false}}else{if(I.match.POS.test(X[0])||I.match.CHILD.test(X[0])){return true}}return X},POS:function(T){T.unshift(true);return T}},filters:{enabled:function(T){return T.disabled===false&&T.type!=="hidden"},disabled:function(T){return T.disabled===true},checked:function(T){return T.checked===true},selected:function(T){T.parentNode.selectedIndex;return T.selected===true},parent:function(T){return !!T.firstChild},empty:function(T){return !T.firstChild},has:function(V,U,T){return !!F(T[3],V).length},header:function(T){return/h\d/i.test(T.nodeName)},text:function(T){return"text"===T.type},radio:function(T){return"radio"===T.type},checkbox:function(T){return"checkbox"===T.type},file:function(T){return"file"===T.type},password:function(T){return"password"===T.type},submit:function(T){return"submit"===T.type},image:function(T){return"image"===T.type},reset:function(T){return"reset"===T.type},button:function(T){return"button"===T.type||T.nodeName.toUpperCase()==="BUTTON"},input:function(T){return/input|select|textarea|button/i.test(T.nodeName)}},setFilters:{first:function(U,T){return T===0},last:function(V,U,T,W){return U===W.length-1},even:function(U,T){return T%2===0},odd:function(U,T){return T%2===1},lt:function(V,U,T){return U<T[3]-0},gt:function(V,U,T){return U>T[3]-0},nth:function(V,U,T){return T[3]-0==U},eq:function(V,U,T){return T[3]-0==U}},filter:{PSEUDO:function(Z,V,W,aa){var U=V[1],X=I.filters[U];if(X){return X(Z,W,V,aa)}else{if(U==="contains"){return(Z.textContent||Z.innerText||"").indexOf(V[3])>=0}else{if(U==="not"){var Y=V[3];for(var W=0,T=Y.length;W<T;W++){if(Y[W]===Z){return false}}return true}}}},CHILD:function(T,W){var Z=W[1],U=T;switch(Z){case"only":case"first":while(U=U.previousSibling){if(U.nodeType===1){return false}}if(Z=="first"){return true}U=T;case"last":while(U=U.nextSibling){if(U.nodeType===1){return false}}return true;case"nth":var V=W[2],ac=W[3];if(V==1&&ac==0){return true}var Y=W[0],ab=T.parentNode;if(ab&&(ab.sizcache!==Y||!T.nodeIndex)){var X=0;for(U=ab.firstChild;U;U=U.nextSibling){if(U.nodeType===1){U.nodeIndex=++X}}ab.sizcache=Y}var aa=T.nodeIndex-ac;if(V==0){return aa==0}else{return(aa%V==0&&aa/V>=0)}}},ID:function(U,T){return U.nodeType===1&&U.getAttribute("id")===T},TAG:function(U,T){return(T==="*"&&U.nodeType===1)||U.nodeName===T},CLASS:function(U,T){return(" "+(U.className||U.getAttribute("class"))+" ").indexOf(T)>-1},ATTR:function(Y,W){var V=W[1],T=I.attrHandle[V]?I.attrHandle[V](Y):Y[V]!=null?Y[V]:Y.getAttribute(V),Z=T+"",X=W[2],U=W[4];return T==null?X==="!=":X==="="?Z===U:X==="*="?Z.indexOf(U)>=0:X==="~="?(" "+Z+" ").indexOf(U)>=0:!U?Z&&T!==false:X==="!="?Z!=U:X==="^="?Z.indexOf(U)===0:X==="$="?Z.substr(Z.length-U.length)===U:X==="|="?Z===U||Z.substr(0,U.length+1)===U+"-":false},POS:function(X,U,V,Y){var T=U[2],W=I.setFilters[T];if(W){return W(X,V,U,Y)}}}};var M=I.match.POS;for(var O in I.match){I.match[O]=RegExp(I.match[O].source+/(?![^\[]*\])(?![^\(]*\))/.source)}var E=function(U,T){U=Array.prototype.slice.call(U);if(T){T.push.apply(T,U);return T}return U};try{Array.prototype.slice.call(document.documentElement.childNodes)}catch(N){E=function(X,W){var U=W||[];if(H.call(X)==="[object Array]"){Array.prototype.push.apply(U,X)}else{if(typeof X.length==="number"){for(var V=0,T=X.length;V<T;V++){U.push(X[V])}}else{for(var V=0;X[V];V++){U.push(X[V])}}}return U}}var G;if(document.documentElement.compareDocumentPosition){G=function(U,T){var V=U.compareDocumentPosition(T)&4?-1:U===T?0:1;if(V===0){hasDuplicate=true}return V}}else{if("sourceIndex" in document.documentElement){G=function(U,T){var V=U.sourceIndex-T.sourceIndex;if(V===0){hasDuplicate=true}return V}}else{if(document.createRange){G=function(W,U){var V=W.ownerDocument.createRange(),T=U.ownerDocument.createRange();V.selectNode(W);V.collapse(true);T.selectNode(U);T.collapse(true);var X=V.compareBoundaryPoints(Range.START_TO_END,T);if(X===0){hasDuplicate=true}return X}}}}(function(){var U=document.createElement("form"),V="script"+(new Date).getTime();U.innerHTML="<input name='"+V+"'/>";var T=document.documentElement;T.insertBefore(U,T.firstChild);if(!!document.getElementById(V)){I.find.ID=function(X,Y,Z){if(typeof Y.getElementById!=="undefined"&&!Z){var W=Y.getElementById(X[1]);return W?W.id===X[1]||typeof W.getAttributeNode!=="undefined"&&W.getAttributeNode("id").nodeValue===X[1]?[W]:g:[]}};I.filter.ID=function(Y,W){var X=typeof Y.getAttributeNode!=="undefined"&&Y.getAttributeNode("id");return Y.nodeType===1&&X&&X.nodeValue===W}}T.removeChild(U)})();(function(){var T=document.createElement("div");T.appendChild(document.createComment(""));if(T.getElementsByTagName("*").length>0){I.find.TAG=function(U,Y){var X=Y.getElementsByTagName(U[1]);if(U[1]==="*"){var W=[];for(var V=0;X[V];V++){if(X[V].nodeType===1){W.push(X[V])}}X=W}return X}}T.innerHTML="<a href='#'></a>";if(T.firstChild&&typeof T.firstChild.getAttribute!=="undefined"&&T.firstChild.getAttribute("href")!=="#"){I.attrHandle.href=function(U){return U.getAttribute("href",2)}}})();if(document.querySelectorAll){(function(){var T=F,U=document.createElement("div");U.innerHTML="<p class='TEST'></p>";if(U.querySelectorAll&&U.querySelectorAll(".TEST").length===0){return}F=function(Y,X,V,W){X=X||document;if(!W&&X.nodeType===9&&!Q(X)){try{return E(X.querySelectorAll(Y),V)}catch(Z){}}return T(Y,X,V,W)};F.find=T.find;F.filter=T.filter;F.selectors=T.selectors;F.matches=T.matches})()}if(document.getElementsByClassName&&document.documentElement.getElementsByClassName){(function(){var T=document.createElement("div");T.innerHTML="<div class='test e'></div><div class='test'></div>";if(T.getElementsByClassName("e").length===0){return}T.lastChild.className="e";if(T.getElementsByClassName("e").length===1){return}I.order.splice(1,0,"CLASS");I.find.CLASS=function(U,V,W){if(typeof V.getElementsByClassName!=="undefined"&&!W){return V.getElementsByClassName(U[1])}}})()}function P(U,Z,Y,ad,aa,ac){var ab=U=="previousSibling"&&!ac;for(var W=0,V=ad.length;W<V;W++){var T=ad[W];if(T){if(ab&&T.nodeType===1){T.sizcache=Y;T.sizset=W}T=T[U];var X=false;while(T){if(T.sizcache===Y){X=ad[T.sizset];break}if(T.nodeType===1&&!ac){T.sizcache=Y;T.sizset=W}if(T.nodeName===Z){X=T;break}T=T[U]}ad[W]=X}}}function S(U,Z,Y,ad,aa,ac){var ab=U=="previousSibling"&&!ac;for(var W=0,V=ad.length;W<V;W++){var T=ad[W];if(T){if(ab&&T.nodeType===1){T.sizcache=Y;T.sizset=W}T=T[U];var X=false;while(T){if(T.sizcache===Y){X=ad[T.sizset];break}if(T.nodeType===1){if(!ac){T.sizcache=Y;T.sizset=W}if(typeof Z!=="string"){if(T===Z){X=true;break}}else{if(F.filter(Z,[T]).length>0){X=T;break}}}T=T[U]}ad[W]=X}}}var K=document.compareDocumentPosition?function(U,T){return U.compareDocumentPosition(T)&16}:function(U,T){return U!==T&&(U.contains?U.contains(T):true)};var Q=function(T){return T.nodeType===9&&T.documentElement.nodeName!=="HTML"||!!T.ownerDocument&&Q(T.ownerDocument)};var J=function(T,aa){var W=[],X="",Y,V=aa.nodeType?[aa]:aa;while((Y=I.match.PSEUDO.exec(T))){X+=Y[0];T=T.replace(I.match.PSEUDO,"")}T=I.relative[T]?T+"*":T;for(var Z=0,U=V.length;Z<U;Z++){F(T,V[Z],W)}return F.filter(X,W)};o.find=F;o.filter=F.filter;o.expr=F.selectors;o.expr[":"]=o.expr.filters;F.selectors.filters.hidden=function(T){return T.offsetWidth===0||T.offsetHeight===0};F.selectors.filters.visible=function(T){return T.offsetWidth>0||T.offsetHeight>0};F.selectors.filters.animated=function(T){return o.grep(o.timers,function(U){return T===U.elem}).length};o.multiFilter=function(V,T,U){if(U){V=":not("+V+")"}return F.matches(V,T)};o.dir=function(V,U){var T=[],W=V[U];while(W&&W!=document){if(W.nodeType==1){T.push(W)}W=W[U]}return T};o.nth=function(X,T,V,W){T=T||1;var U=0;for(;X;X=X[V]){if(X.nodeType==1&&++U==T){break}}return X};o.sibling=function(V,U){var T=[];for(;V;V=V.nextSibling){if(V.nodeType==1&&V!=U){T.push(V)}}return T};return;l.Sizzle=F})();o.event={add:function(I,F,H,K){if(I.nodeType==3||I.nodeType==8){return}if(I.setInterval&&I!=l){I=l}if(!H.guid){H.guid=this.guid++}if(K!==g){var G=H;H=this.proxy(G);H.data=K}var E=o.data(I,"events")||o.data(I,"events",{}),J=o.data(I,"handle")||o.data(I,"handle",function(){return typeof o!=="undefined"&&!o.event.triggered?o.event.handle.apply(arguments.callee.elem,arguments):g});J.elem=I;o.each(F.split(/\s+/),function(M,N){var O=N.split(".");N=O.shift();H.type=O.slice().sort().join(".");var L=E[N];if(o.event.specialAll[N]){o.event.specialAll[N].setup.call(I,K,O)}if(!L){L=E[N]={};if(!o.event.special[N]||o.event.special[N].setup.call(I,K,O)===false){if(I.addEventListener){I.addEventListener(N,J,false)}else{if(I.attachEvent){I.attachEvent("on"+N,J)}}}}L[H.guid]=H;o.event.global[N]=true});I=null},guid:1,global:{},remove:function(K,H,J){if(K.nodeType==3||K.nodeType==8){return}var G=o.data(K,"events"),F,E;if(G){if(H===g||(typeof H==="string"&&H.charAt(0)==".")){for(var I in G){this.remove(K,I+(H||""))}}else{if(H.type){J=H.handler;H=H.type}o.each(H.split(/\s+/),function(M,O){var Q=O.split(".");O=Q.shift();var N=RegExp("(^|\\.)"+Q.slice().sort().join(".*\\.")+"(\\.|$)");if(G[O]){if(J){delete G[O][J.guid]}else{for(var P in G[O]){if(N.test(G[O][P].type)){delete G[O][P]}}}if(o.event.specialAll[O]){o.event.specialAll[O].teardown.call(K,Q)}for(F in G[O]){break}if(!F){if(!o.event.special[O]||o.event.special[O].teardown.call(K,Q)===false){if(K.removeEventListener){K.removeEventListener(O,o.data(K,"handle"),false)}else{if(K.detachEvent){K.detachEvent("on"+O,o.data(K,"handle"))}}}F=null;delete G[O]}}})}for(F in G){break}if(!F){var L=o.data(K,"handle");if(L){L.elem=null}o.removeData(K,"events");o.removeData(K,"handle")}}},trigger:function(I,K,H,E){var G=I.type||I;if(!E){I=typeof I==="object"?I[h]?I:o.extend(o.Event(G),I):o.Event(G);if(G.indexOf("!")>=0){I.type=G=G.slice(0,-1);I.exclusive=true}if(!H){I.stopPropagation();if(this.global[G]){o.each(o.cache,function(){if(this.events&&this.events[G]){o.event.trigger(I,K,this.handle.elem)}})}}if(!H||H.nodeType==3||H.nodeType==8){return g}I.result=g;I.target=H;K=o.makeArray(K);K.unshift(I)}I.currentTarget=H;var J=o.data(H,"handle");if(J){J.apply(H,K)}if((!H[G]||(o.nodeName(H,"a")&&G=="click"))&&H["on"+G]&&H["on"+G].apply(H,K)===false){I.result=false}if(!E&&H[G]&&!I.isDefaultPrevented()&&!(o.nodeName(H,"a")&&G=="click")){this.triggered=true;try{H[G]()}catch(L){}}this.triggered=false;if(!I.isPropagationStopped()){var F=H.parentNode||H.ownerDocument;if(F){o.event.trigger(I,K,F,true)}}},handle:function(K){var J,E;K=arguments[0]=o.event.fix(K||l.event);K.currentTarget=this;var L=K.type.split(".");K.type=L.shift();J=!L.length&&!K.exclusive;var I=RegExp("(^|\\.)"+L.slice().sort().join(".*\\.")+"(\\.|$)");E=(o.data(this,"events")||{})[K.type];for(var G in E){var H=E[G];if(J||I.test(H.type)){K.handler=H;K.data=H.data;var F=H.apply(this,arguments);if(F!==g){K.result=F;if(F===false){K.preventDefault();K.stopPropagation()}}if(K.isImmediatePropagationStopped()){break}}}},props:"altKey attrChange attrName bubbles button cancelable charCode clientX clientY ctrlKey currentTarget data detail eventPhase fromElement handler keyCode metaKey newValue originalTarget pageX pageY prevValue relatedNode relatedTarget screenX screenY shiftKey srcElement target toElement view wheelDelta which".split(" "),fix:function(H){if(H[h]){return H}var F=H;H=o.Event(F);for(var G=this.props.length,J;G;){J=this.props[--G];H[J]=F[J]}if(!H.target){H.target=H.srcElement||document}if(H.target.nodeType==3){H.target=H.target.parentNode}if(!H.relatedTarget&&H.fromElement){H.relatedTarget=H.fromElement==H.target?H.toElement:H.fromElement}if(H.pageX==null&&H.clientX!=null){var I=document.documentElement,E=document.body;H.pageX=H.clientX+(I&&I.scrollLeft||E&&E.scrollLeft||0)-(I.clientLeft||0);H.pageY=H.clientY+(I&&I.scrollTop||E&&E.scrollTop||0)-(I.clientTop||0)}if(!H.which&&((H.charCode||H.charCode===0)?H.charCode:H.keyCode)){H.which=H.charCode||H.keyCode}if(!H.metaKey&&H.ctrlKey){H.metaKey=H.ctrlKey}if(!H.which&&H.button){H.which=(H.button&1?1:(H.button&2?3:(H.button&4?2:0)))}return H},proxy:function(F,E){E=E||function(){return F.apply(this,arguments)};E.guid=F.guid=F.guid||E.guid||this.guid++;return E},special:{ready:{setup:B,teardown:function(){}}},specialAll:{live:{setup:function(E,F){o.event.add(this,F[0],c)},teardown:function(G){if(G.length){var E=0,F=RegExp("(^|\\.)"+G[0]+"(\\.|$)");o.each((o.data(this,"events").live||{}),function(){if(F.test(this.type)){E++}});if(E<1){o.event.remove(this,G[0],c)}}}}}};o.Event=function(E){if(!this.preventDefault){return new o.Event(E)}if(E&&E.type){this.originalEvent=E;this.type=E.type}else{this.type=E}this.timeStamp=e();this[h]=true};function k(){return false}function u(){return true}o.Event.prototype={preventDefault:function(){this.isDefaultPrevented=u;var E=this.originalEvent;if(!E){return}if(E.preventDefault){E.preventDefault()}E.returnValue=false},stopPropagation:function(){this.isPropagationStopped=u;var E=this.originalEvent;if(!E){return}if(E.stopPropagation){E.stopPropagation()}E.cancelBubble=true},stopImmediatePropagation:function(){this.isImmediatePropagationStopped=u;this.stopPropagation()},isDefaultPrevented:k,isPropagationStopped:k,isImmediatePropagationStopped:k};var a=function(F){var E=F.relatedTarget;while(E&&E!=this){try{E=E.parentNode}catch(G){E=this}}if(E!=this){F.type=F.data;o.event.handle.apply(this,arguments)}};o.each({mouseover:"mouseenter",mouseout:"mouseleave"},function(F,E){o.event.special[E]={setup:function(){o.event.add(this,F,a,E)},teardown:function(){o.event.remove(this,F,a)}}});o.fn.extend({bind:function(F,G,E){return F=="unload"?this.one(F,G,E):this.each(function(){o.event.add(this,F,E||G,E&&G)})},one:function(G,H,F){var E=o.event.proxy(F||H,function(I){o(this).unbind(I,E);return(F||H).apply(this,arguments)});return this.each(function(){o.event.add(this,G,E,F&&H)})},unbind:function(F,E){return this.each(function(){o.event.remove(this,F,E)})},trigger:function(E,F){return this.each(function(){o.event.trigger(E,F,this)})},triggerHandler:function(E,G){if(this[0]){var F=o.Event(E);F.preventDefault();F.stopPropagation();o.event.trigger(F,G,this[0]);return F.result}},toggle:function(G){var E=arguments,F=1;while(F<E.length){o.event.proxy(G,E[F++])}return this.click(o.event.proxy(G,function(H){this.lastToggle=(this.lastToggle||0)%F;H.preventDefault();return E[this.lastToggle++].apply(this,arguments)||false}))},hover:function(E,F){return this.mouseenter(E).mouseleave(F)},ready:function(E){B();if(o.isReady){E.call(document,o)}else{o.readyList.push(E)}return this},live:function(G,F){var E=o.event.proxy(F);E.guid+=this.selector+G;o(document).bind(i(G,this.selector),this.selector,E);return this},die:function(F,E){o(document).unbind(i(F,this.selector),E?{guid:E.guid+this.selector+F}:null);return this}});function c(H){var E=RegExp("(^|\\.)"+H.type+"(\\.|$)"),G=true,F=[];o.each(o.data(this,"events").live||[],function(I,J){if(E.test(J.type)){var K=o(H.target).closest(J.data)[0];if(K){F.push({elem:K,fn:J})}}});F.sort(function(J,I){return o.data(J.elem,"closest")-o.data(I.elem,"closest")});o.each(F,function(){if(this.fn.call(this.elem,H,this.fn.data)===false){return(G=false)}});return G}function i(F,E){return["live",F,E.replace(/\./g,"`").replace(/ /g,"|")].join(".")}o.extend({isReady:false,readyList:[],ready:function(){if(!o.isReady){o.isReady=true;if(o.readyList){o.each(o.readyList,function(){this.call(document,o)});o.readyList=null}o(document).triggerHandler("ready")}}});var x=false;function B(){if(x){return}x=true;if(document.addEventListener){document.addEventListener("DOMContentLoaded",function(){document.removeEventListener("DOMContentLoaded",arguments.callee,false);o.ready()},false)}else{if(document.attachEvent){document.attachEvent("onreadystatechange",function(){if(document.readyState==="complete"){document.detachEvent("onreadystatechange",arguments.callee);o.ready()}});if(document.documentElement.doScroll&&l==l.top){(function(){if(o.isReady){return}try{document.documentElement.doScroll("left")}catch(E){setTimeout(arguments.callee,0);return}o.ready()})()}}}o.event.add(l,"load",o.ready)}o.each(("blur,focus,load,resize,scroll,unload,click,dblclick,mousedown,mouseup,mousemove,mouseover,mouseout,mouseenter,mouseleave,change,select,submit,keydown,keypress,keyup,error").split(","),function(F,E){o.fn[E]=function(G){return G?this.bind(E,G):this.trigger(E)}});o(l).bind("unload",function(){for(var E in o.cache){if(E!=1&&o.cache[E].handle){o.event.remove(o.cache[E].handle.elem)}}});(function(){o.support={};var F=document.documentElement,G=document.createElement("script"),K=document.createElement("div"),J="script"+(new Date).getTime();K.style.display="none";K.innerHTML='   <link/><table></table><a href="/a" style="color:red;float:left;opacity:.5;">a</a><select><option>text</option></select><object><param/></object>';var H=K.getElementsByTagName("*"),E=K.getElementsByTagName("a")[0];if(!H||!H.length||!E){return}o.support={leadingWhitespace:K.firstChild.nodeType==3,tbody:!K.getElementsByTagName("tbody").length,objectAll:!!K.getElementsByTagName("object")[0].getElementsByTagName("*").length,htmlSerialize:!!K.getElementsByTagName("link").length,style:/red/.test(E.getAttribute("style")),hrefNormalized:E.getAttribute("href")==="/a",opacity:E.style.opacity==="0.5",cssFloat:!!E.style.cssFloat,scriptEval:false,noCloneEvent:true,boxModel:null};G.type="text/javascript";try{G.appendChild(document.createTextNode("window."+J+"=1;"))}catch(I){}F.insertBefore(G,F.firstChild);if(l[J]){o.support.scriptEval=true;delete l[J]}F.removeChild(G);if(K.attachEvent&&K.fireEvent){K.attachEvent("onclick",function(){o.support.noCloneEvent=false;K.detachEvent("onclick",arguments.callee)});K.cloneNode(true).fireEvent("onclick")}o(function(){var L=document.createElement("div");L.style.width=L.style.paddingLeft="1px";document.body.appendChild(L);o.boxModel=o.support.boxModel=L.offsetWidth===2;document.body.removeChild(L).style.display="none"})})();var w=o.support.cssFloat?"cssFloat":"styleFloat";o.props={"for":"htmlFor","class":"className","float":w,cssFloat:w,styleFloat:w,readonly:"readOnly",maxlength:"maxLength",cellspacing:"cellSpacing",rowspan:"rowSpan",tabindex:"tabIndex"};o.fn.extend({_load:o.fn.load,load:function(G,J,K){if(typeof G!=="string"){return this._load(G)}var I=G.indexOf(" ");if(I>=0){var E=G.slice(I,G.length);G=G.slice(0,I)}var H="GET";if(J){if(o.isFunction(J)){K=J;J=null}else{if(typeof J==="object"){J=o.param(J);H="POST"}}}var F=this;o.ajax({url:G,type:H,dataType:"html",data:J,complete:function(M,L){if(L=="success"||L=="notmodified"){F.html(E?o("<div/>").append(M.responseText.replace(/<script(.|\s)*?\/script>/g,"")).find(E):M.responseText)}if(K){F.each(K,[M.responseText,L,M])}}});return this},serialize:function(){return o.param(this.serializeArray())},serializeArray:function(){return this.map(function(){return this.elements?o.makeArray(this.elements):this}).filter(function(){return this.name&&!this.disabled&&(this.checked||/select|textarea/i.test(this.nodeName)||/text|hidden|password|search/i.test(this.type))}).map(function(E,F){var G=o(this).val();return G==null?null:o.isArray(G)?o.map(G,function(I,H){return{name:F.name,value:I}}):{name:F.name,value:G}}).get()}});o.each("ajaxStart,ajaxStop,ajaxComplete,ajaxError,ajaxSuccess,ajaxSend".split(","),function(E,F){o.fn[F]=function(G){return this.bind(F,G)}});var r=e();o.extend({get:function(E,G,H,F){if(o.isFunction(G)){H=G;G=null}return o.ajax({type:"GET",url:E,data:G,success:H,dataType:F})},getScript:function(E,F){return o.get(E,null,F,"script")},getJSON:function(E,F,G){return o.get(E,F,G,"json")},post:function(E,G,H,F){if(o.isFunction(G)){H=G;G={}}return o.ajax({type:"POST",url:E,data:G,success:H,dataType:F})},ajaxSetup:function(E){o.extend(o.ajaxSettings,E)},ajaxSettings:{url:location.href,global:true,type:"GET",contentType:"application/x-www-form-urlencoded",processData:true,async:true,xhr:function(){return l.ActiveXObject?new ActiveXObject("Microsoft.XMLHTTP"):new XMLHttpRequest()},accepts:{xml:"application/xml, text/xml",html:"text/html",script:"text/javascript, application/javascript",json:"application/json, text/javascript",text:"text/plain",_default:"*/*"}},lastModified:{},ajax:function(M){M=o.extend(true,M,o.extend(true,{},o.ajaxSettings,M));var W,F=/=\?(&|$)/g,R,V,G=M.type.toUpperCase();if(M.data&&M.processData&&typeof M.data!=="string"){M.data=o.param(M.data)}if(M.dataType=="jsonp"){if(G=="GET"){if(!M.url.match(F)){M.url+=(M.url.match(/\?/)?"&":"?")+(M.jsonp||"callback")+"=?"}}else{if(!M.data||!M.data.match(F)){M.data=(M.data?M.data+"&":"")+(M.jsonp||"callback")+"=?"}}M.dataType="json"}if(M.dataType=="json"&&(M.data&&M.data.match(F)||M.url.match(F))){W="jsonp"+r++;if(M.data){M.data=(M.data+"").replace(F,"="+W+"$1")}M.url=M.url.replace(F,"="+W+"$1");M.dataType="script";l[W]=function(X){V=X;I();L();l[W]=g;try{delete l[W]}catch(Y){}if(H){H.removeChild(T)}}}if(M.dataType=="script"&&M.cache==null){M.cache=false}if(M.cache===false&&G=="GET"){var E=e();var U=M.url.replace(/(\?|&)_=.*?(&|$)/,"$1_="+E+"$2");M.url=U+((U==M.url)?(M.url.match(/\?/)?"&":"?")+"_="+E:"")}if(M.data&&G=="GET"){M.url+=(M.url.match(/\?/)?"&":"?")+M.data;M.data=null}if(M.global&&!o.active++){o.event.trigger("ajaxStart")}var Q=/^(\w+:)?\/\/([^\/?#]+)/.exec(M.url);if(M.dataType=="script"&&G=="GET"&&Q&&(Q[1]&&Q[1]!=location.protocol||Q[2]!=location.host)){var H=document.getElementsByTagName("head")[0];var T=document.createElement("script");T.src=M.url;if(M.scriptCharset){T.charset=M.scriptCharset}if(!W){var O=false;T.onload=T.onreadystatechange=function(){if(!O&&(!this.readyState||this.readyState=="loaded"||this.readyState=="complete")){O=true;I();L();T.onload=T.onreadystatechange=null;H.removeChild(T)}}}H.appendChild(T);return g}var K=false;var J=M.xhr();if(M.username){J.open(G,M.url,M.async,M.username,M.password)}else{J.open(G,M.url,M.async)}try{if(M.data){J.setRequestHeader("Content-Type",M.contentType)}if(M.ifModified){J.setRequestHeader("If-Modified-Since",o.lastModified[M.url]||"Thu, 01 Jan 1970 00:00:00 GMT")}J.setRequestHeader("X-Requested-With","XMLHttpRequest");J.setRequestHeader("Accept",M.dataType&&M.accepts[M.dataType]?M.accepts[M.dataType]+", */*":M.accepts._default)}catch(S){}if(M.beforeSend&&M.beforeSend(J,M)===false){if(M.global&&!--o.active){o.event.trigger("ajaxStop")}J.abort();return false}if(M.global){o.event.trigger("ajaxSend",[J,M])}var N=function(X){if(J.readyState==0){if(P){clearInterval(P);P=null;if(M.global&&!--o.active){o.event.trigger("ajaxStop")}}}else{if(!K&&J&&(J.readyState==4||X=="timeout")){K=true;if(P){clearInterval(P);P=null}R=X=="timeout"?"timeout":!o.httpSuccess(J)?"error":M.ifModified&&o.httpNotModified(J,M.url)?"notmodified":"success";if(R=="success"){try{V=o.httpData(J,M.dataType,M)}catch(Z){R="parsererror"}}if(R=="success"){var Y;try{Y=J.getResponseHeader("Last-Modified")}catch(Z){}if(M.ifModified&&Y){o.lastModified[M.url]=Y}if(!W){I()}}else{o.handleError(M,J,R)}L();if(X){J.abort()}if(M.async){J=null}}}};if(M.async){var P=setInterval(N,13);if(M.timeout>0){setTimeout(function(){if(J&&!K){N("timeout")}},M.timeout)}}try{J.send(M.data)}catch(S){o.handleError(M,J,null,S)}if(!M.async){N()}function I(){if(M.success){M.success(V,R)}if(M.global){o.event.trigger("ajaxSuccess",[J,M])}}function L(){if(M.complete){M.complete(J,R)}if(M.global){o.event.trigger("ajaxComplete",[J,M])}if(M.global&&!--o.active){o.event.trigger("ajaxStop")}}return J},handleError:function(F,H,E,G){if(F.error){F.error(H,E,G)}if(F.global){o.event.trigger("ajaxError",[H,F,G])}},active:0,httpSuccess:function(F){try{return !F.status&&location.protocol=="file:"||(F.status>=200&&F.status<300)||F.status==304||F.status==1223}catch(E){}return false},httpNotModified:function(G,E){try{var H=G.getResponseHeader("Last-Modified");return G.status==304||H==o.lastModified[E]}catch(F){}return false},httpData:function(J,H,G){var F=J.getResponseHeader("content-type"),E=H=="xml"||!H&&F&&F.indexOf("xml")>=0,I=E?J.responseXML:J.responseText;if(E&&I.documentElement.tagName=="parsererror"){throw"parsererror"}if(G&&G.dataFilter){I=G.dataFilter(I,H)}if(typeof I==="string"){if(H=="script"){o.globalEval(I)}if(H=="json"){I=l["eval"]("("+I+")")}}return I},param:function(E){var G=[];function H(I,J){G[G.length]=encodeURIComponent(I)+"="+encodeURIComponent(J)}if(o.isArray(E)||E.jquery){o.each(E,function(){H(this.name,this.value)})}else{for(var F in E){if(o.isArray(E[F])){o.each(E[F],function(){H(F,this)})}else{H(F,o.isFunction(E[F])?E[F]():E[F])}}}return G.join("&").replace(/%20/g,"+")}});var m={},n,d=[["height","marginTop","marginBottom","paddingTop","paddingBottom"],["width","marginLeft","marginRight","paddingLeft","paddingRight"],["opacity"]];function t(F,E){var G={};o.each(d.concat.apply([],d.slice(0,E)),function(){G[this]=F});return G}o.fn.extend({show:function(J,L){if(J){return this.animate(t("show",3),J,L)}else{for(var H=0,F=this.length;H<F;H++){var E=o.data(this[H],"olddisplay");this[H].style.display=E||"";if(o.css(this[H],"display")==="none"){var G=this[H].tagName,K;if(m[G]){K=m[G]}else{var I=o("<"+G+" />").appendTo("body");K=I.css("display");if(K==="none"){K="block"}I.remove();m[G]=K}o.data(this[H],"olddisplay",K)}}for(var H=0,F=this.length;H<F;H++){this[H].style.display=o.data(this[H],"olddisplay")||""}return this}},hide:function(H,I){if(H){return this.animate(t("hide",3),H,I)}else{for(var G=0,F=this.length;G<F;G++){var E=o.data(this[G],"olddisplay");if(!E&&E!=="none"){o.data(this[G],"olddisplay",o.css(this[G],"display"))}}for(var G=0,F=this.length;G<F;G++){this[G].style.display="none"}return this}},_toggle:o.fn.toggle,toggle:function(G,F){var E=typeof G==="boolean";return o.isFunction(G)&&o.isFunction(F)?this._toggle.apply(this,arguments):G==null||E?this.each(function(){var H=E?G:o(this).is(":hidden");o(this)[H?"show":"hide"]()}):this.animate(t("toggle",3),G,F)},fadeTo:function(E,G,F){return this.animate({opacity:G},E,F)},animate:function(I,F,H,G){var E=o.speed(F,H,G);return this[E.queue===false?"each":"queue"](function(){var K=o.extend({},E),M,L=this.nodeType==1&&o(this).is(":hidden"),J=this;for(M in I){if(I[M]=="hide"&&L||I[M]=="show"&&!L){return K.complete.call(this)}if((M=="height"||M=="width")&&this.style){K.display=o.css(this,"display");K.overflow=this.style.overflow}}if(K.overflow!=null){this.style.overflow="hidden"}K.curAnim=o.extend({},I);o.each(I,function(O,S){var R=new o.fx(J,K,O);if(/toggle|show|hide/.test(S)){R[S=="toggle"?L?"show":"hide":S](I)}else{var Q=S.toString().match(/^([+-]=)?([\d+-.]+)(.*)$/),T=R.cur(true)||0;if(Q){var N=parseFloat(Q[2]),P=Q[3]||"px";if(P!="px"){J.style[O]=(N||1)+P;T=((N||1)/R.cur(true))*T;J.style[O]=T+P}if(Q[1]){N=((Q[1]=="-="?-1:1)*N)+T}R.custom(T,N,P)}else{R.custom(T,S,"")}}});return true})},stop:function(F,E){var G=o.timers;if(F){this.queue([])}this.each(function(){for(var H=G.length-1;H>=0;H--){if(G[H].elem==this){if(E){G[H](true)}G.splice(H,1)}}});if(!E){this.dequeue()}return this}});o.each({slideDown:t("show",1),slideUp:t("hide",1),slideToggle:t("toggle",1),fadeIn:{opacity:"show"},fadeOut:{opacity:"hide"}},function(E,F){o.fn[E]=function(G,H){return this.animate(F,G,H)}});o.extend({speed:function(G,H,F){var E=typeof G==="object"?G:{complete:F||!F&&H||o.isFunction(G)&&G,duration:G,easing:F&&H||H&&!o.isFunction(H)&&H};E.duration=o.fx.off?0:typeof E.duration==="number"?E.duration:o.fx.speeds[E.duration]||o.fx.speeds._default;E.old=E.complete;E.complete=function(){if(E.queue!==false){o(this).dequeue()}if(o.isFunction(E.old)){E.old.call(this)}};return E},easing:{linear:function(G,H,E,F){return E+F*G},swing:function(G,H,E,F){return((-Math.cos(G*Math.PI)/2)+0.5)*F+E}},timers:[],fx:function(F,E,G){this.options=E;this.elem=F;this.prop=G;if(!E.orig){E.orig={}}}});o.fx.prototype={update:function(){if(this.options.step){this.options.step.call(this.elem,this.now,this)}(o.fx.step[this.prop]||o.fx.step._default)(this);if((this.prop=="height"||this.prop=="width")&&this.elem.style){this.elem.style.display="block"}},cur:function(F){if(this.elem[this.prop]!=null&&(!this.elem.style||this.elem.style[this.prop]==null)){return this.elem[this.prop]}var E=parseFloat(o.css(this.elem,this.prop,F));return E&&E>-10000?E:parseFloat(o.curCSS(this.elem,this.prop))||0},custom:function(I,H,G){this.startTime=e();this.start=I;this.end=H;this.unit=G||this.unit||"px";this.now=this.start;this.pos=this.state=0;var E=this;function F(J){return E.step(J)}F.elem=this.elem;if(F()&&o.timers.push(F)&&!n){n=setInterval(function(){var K=o.timers;for(var J=0;J<K.length;J++){if(!K[J]()){K.splice(J--,1)}}if(!K.length){clearInterval(n);n=g}},13)}},show:function(){this.options.orig[this.prop]=o.attr(this.elem.style,this.prop);this.options.show=true;this.custom(this.prop=="width"||this.prop=="height"?1:0,this.cur());o(this.elem).show()},hide:function(){this.options.orig[this.prop]=o.attr(this.elem.style,this.prop);this.options.hide=true;this.custom(this.cur(),0)},step:function(H){var G=e();if(H||G>=this.options.duration+this.startTime){this.now=this.end;this.pos=this.state=1;this.update();this.options.curAnim[this.prop]=true;var E=true;for(var F in this.options.curAnim){if(this.options.curAnim[F]!==true){E=false}}if(E){if(this.options.display!=null){this.elem.style.overflow=this.options.overflow;this.elem.style.display=this.options.display;if(o.css(this.elem,"display")=="none"){this.elem.style.display="block"}}if(this.options.hide){o(this.elem).hide()}if(this.options.hide||this.options.show){for(var I in this.options.curAnim){o.attr(this.elem.style,I,this.options.orig[I])}}this.options.complete.call(this.elem)}return false}else{var J=G-this.startTime;this.state=J/this.options.duration;this.pos=o.easing[this.options.easing||(o.easing.swing?"swing":"linear")](this.state,J,0,1,this.options.duration);this.now=this.start+((this.end-this.start)*this.pos);this.update()}return true}};o.extend(o.fx,{speeds:{slow:600,fast:200,_default:400},step:{opacity:function(E){o.attr(E.elem.style,"opacity",E.now)},_default:function(E){if(E.elem.style&&E.elem.style[E.prop]!=null){E.elem.style[E.prop]=E.now+E.unit}else{E.elem[E.prop]=E.now}}}});if(document.documentElement.getBoundingClientRect){o.fn.offset=function(){if(!this[0]){return{top:0,left:0}}if(this[0]===this[0].ownerDocument.body){return o.offset.bodyOffset(this[0])}var G=this[0].getBoundingClientRect(),J=this[0].ownerDocument,F=J.body,E=J.documentElement,L=E.clientTop||F.clientTop||0,K=E.clientLeft||F.clientLeft||0,I=G.top+(self.pageYOffset||o.boxModel&&E.scrollTop||F.scrollTop)-L,H=G.left+(self.pageXOffset||o.boxModel&&E.scrollLeft||F.scrollLeft)-K;return{top:I,left:H}}}else{o.fn.offset=function(){if(!this[0]){return{top:0,left:0}}if(this[0]===this[0].ownerDocument.body){return o.offset.bodyOffset(this[0])}o.offset.initialized||o.offset.initialize();var J=this[0],G=J.offsetParent,F=J,O=J.ownerDocument,M,H=O.documentElement,K=O.body,L=O.defaultView,E=L.getComputedStyle(J,null),N=J.offsetTop,I=J.offsetLeft;while((J=J.parentNode)&&J!==K&&J!==H){M=L.getComputedStyle(J,null);N-=J.scrollTop,I-=J.scrollLeft;if(J===G){N+=J.offsetTop,I+=J.offsetLeft;if(o.offset.doesNotAddBorder&&!(o.offset.doesAddBorderForTableAndCells&&/^t(able|d|h)$/i.test(J.tagName))){N+=parseInt(M.borderTopWidth,10)||0,I+=parseInt(M.borderLeftWidth,10)||0}F=G,G=J.offsetParent}if(o.offset.subtractsBorderForOverflowNotVisible&&M.overflow!=="visible"){N+=parseInt(M.borderTopWidth,10)||0,I+=parseInt(M.borderLeftWidth,10)||0}E=M}if(E.position==="relative"||E.position==="static"){N+=K.offsetTop,I+=K.offsetLeft}if(E.position==="fixed"){N+=Math.max(H.scrollTop,K.scrollTop),I+=Math.max(H.scrollLeft,K.scrollLeft)}return{top:N,left:I}}}o.offset={initialize:function(){if(this.initialized){return}var L=document.body,F=document.createElement("div"),H,G,N,I,M,E,J=L.style.marginTop,K='<div style="position:absolute;top:0;left:0;margin:0;border:5px solid #000;padding:0;width:1px;height:1px;"><div></div></div><table style="position:absolute;top:0;left:0;margin:0;border:5px solid #000;padding:0;width:1px;height:1px;" cellpadding="0" cellspacing="0"><tr><td></td></tr></table>';M={position:"absolute",top:0,left:0,margin:0,border:0,width:"1px",height:"1px",visibility:"hidden"};for(E in M){F.style[E]=M[E]}F.innerHTML=K;L.insertBefore(F,L.firstChild);H=F.firstChild,G=H.firstChild,I=H.nextSibling.firstChild.firstChild;this.doesNotAddBorder=(G.offsetTop!==5);this.doesAddBorderForTableAndCells=(I.offsetTop===5);H.style.overflow="hidden",H.style.position="relative";this.subtractsBorderForOverflowNotVisible=(G.offsetTop===-5);L.style.marginTop="1px";this.doesNotIncludeMarginInBodyOffset=(L.offsetTop===0);L.style.marginTop=J;L.removeChild(F);this.initialized=true},bodyOffset:function(E){o.offset.initialized||o.offset.initialize();var G=E.offsetTop,F=E.offsetLeft;if(o.offset.doesNotIncludeMarginInBodyOffset){G+=parseInt(o.curCSS(E,"marginTop",true),10)||0,F+=parseInt(o.curCSS(E,"marginLeft",true),10)||0}return{top:G,left:F}}};o.fn.extend({position:function(){var I=0,H=0,F;if(this[0]){var G=this.offsetParent(),J=this.offset(),E=/^body|html$/i.test(G[0].tagName)?{top:0,left:0}:G.offset();J.top-=j(this,"marginTop");J.left-=j(this,"marginLeft");E.top+=j(G,"borderTopWidth");E.left+=j(G,"borderLeftWidth");F={top:J.top-E.top,left:J.left-E.left}}return F},offsetParent:function(){var E=this[0].offsetParent||document.body;while(E&&(!/^body|html$/i.test(E.tagName)&&o.css(E,"position")=="static")){E=E.offsetParent}return o(E)}});o.each(["Left","Top"],function(F,E){var G="scroll"+E;o.fn[G]=function(H){if(!this[0]){return null}return H!==g?this.each(function(){this==l||this==document?l.scrollTo(!F?H:o(l).scrollLeft(),F?H:o(l).scrollTop()):this[G]=H}):this[0]==l||this[0]==document?self[F?"pageYOffset":"pageXOffset"]||o.boxModel&&document.documentElement[G]||document.body[G]:this[0][G]}});o.each(["Height","Width"],function(I,G){var E=I?"Left":"Top",H=I?"Right":"Bottom",F=G.toLowerCase();o.fn["inner"+G]=function(){return this[0]?o.css(this[0],F,false,"padding"):null};o.fn["outer"+G]=function(K){return this[0]?o.css(this[0],F,false,K?"margin":"border"):null};var J=G.toLowerCase();o.fn[J]=function(K){return this[0]==l?document.compatMode=="CSS1Compat"&&document.documentElement["client"+G]||document.body["client"+G]:this[0]==document?Math.max(document.documentElement["client"+G],document.body["scroll"+G],document.documentElement["scroll"+G],document.body["offset"+G],document.documentElement["offset"+G]):K===g?(this.length?o.css(this[0],J):null):this.css(J,typeof K==="string"?K:K+"px")}})})();

//if ( document.location.href.indexOf("post.forum",1) == -1 && document.location.href.indexOf("posting.forum",1) == -1
//&& document.location.href.indexOf("msg.forum",1) == -1 && document.location.href.indexOf("groupcp.forum",1) == -1
//&& document.location.href.indexOf("/adminv1/",1) == -1 && document.location.href.indexOf("/admin/",1) == -1 && document.location.href.indexOf("donate.forum",1) == -1
//&& document.location.href.indexOf("report.forum",1) == -1 && document.location.href.indexOf("calendar_event.forum",1) == -1
//&& document.location.href.indexOf("privmsg.forum",1) == -1 && document.location.href.indexOf("chatbox",1) == -1 && document.location.href.indexOf("/gallery/",1) == -1
//&& document.location.href.indexOf("profile.forum",1) == -1)
//{
//	window.open = false;
//}
function resize_images() {
	for (i = 0; i < document.images.length; i++) {
		while ( !document.images[i].complete ) {
			break;
		}
		if ( document.images[i].width > 900 ) {
			document.images[i].width = 900;
		}
	}
}
// Startup variables
var imageTag = false;
var theSelection = false;
// Check for Browser & Platform for PC & IE specific bits
// More details from: http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion); // Get browser version
var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
var is_nav = ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1)
&& (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1)
&& (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));
var is_moz = 0;
var is_win = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));
var is_mac = (clientPC.indexOf("mac")!=-1);
// Helpline messages
b_help = "Texte gras: [b]texte[/b] (alt+b)";
i_help = "Texte italique: [i]texte[/i] (alt+i)";
u_help = "Texte souligné: [u]texte[/u] (alt+u)";
q_help = "Citation: [quote]texte cité[/quote] (alt+q)";
c_help = "Afficher du code: [code]code[/code] (alt+c)";
l_help = "Liste: [list]texte[/list] (alt+l)";
o_help = "Liste ordonnée: [list=]texte[/list] (alt+o)";
p_help = "Insérer une image: [img]http://image_url/[/img] (alt+p)";
w_help = "Insérer un lien: [url]http://url/[/url] ou [url=http://url/]Nom[/url] (alt+w)";
a_help = "Fermer toutes les balises BBCode ouvertes";
s_help = "Couleur du texte: [color=red]texte[/color] Astuce: #FF0000 fonctionne aussi";
f_help = "Taille du texte: [size=x-small]texte en petit[/size]";
k_help = "Texte Défilant: [scroll]texte[/scroll] (alt+k)";
e_help = "Texte Pâle: [fade]texte[/fade] (alt+e)";
r_help = "Texte Flou: [blur]texte[/blur] (alt+r)";
j_help = "Texte Renversé: [flipv]texte[/flipv] (alt+v)";
v_help = "Texte Renversé: [fliph]texte[/fliph] (alt+j)";
m_help = "Texte aligné à gauche: [left]texte[/left] (alt+m)";
d_help = "Texte défilant de haut en bas: [updown]texte[/updown] (alt+d)";
t_help = "Texte centré: [center]texte[/center] (alt+t)";
g_help = "Texte aligné à droite: [right]texte[/right] (alt+g)";
x_help = "Texte barré: [strike]texte[/strike] (alt+x)";
y_help = "Héberger une image";
z_help = "Insérer une émoticon dans votre message";
h_help = "Texte visible par ceux qui ont posté dans ce sujet : [hide]texte[/hide] (alt+h)";
sp_help = "Texte caché et affiché en cliquant dessus: [spoiler]texte[/spoiler] (alt+o)";
wo_help = "Un objet World of Warcraft : [wow]17104[/wow]";
ft_help = "Police du texte : [font=Verdana]texte[/font]";
jt_help = "Texte justifié: [justify]texte[/justify] (alt+j)";
sub_help = "Mettre en indice: [sub]texte[/sub] (alt+m)";
sup_help = "Mettre en exposant: [sup]texte[/sup] (alt+n)";
tab_help = "Insérer un tableau";
hr_help = "Insérer une ligne : texte[hr]texte";
fl_help = "Insérer du flash : [flash(largeur,hauteur)]url[/flash]";
vd_help = "Insérer une vidéo (youtube, dailymotion)";
_help = "";

// Define the bbCode tags
bbcode = new Array();
bbtags = new Array('[b]','[/b]','[i]','[/i]','[u]','[/u]','[quote]','[/quote]','[code]','[/code]','[list][*]','[/list]','[list=1][*]','[/list]','[img]','[/img]','[url]','[/url]','[scroll]','[/scroll]','[fade]','[/fade]','[blur]','[/blur]','[flipv]','[/flipv]','[fliph]','[/fliph]','[updown]','[/updown]','[center]','[/center]','[right]','[/right]','[strike]','[/strike]','[embed-flash(width,height)]','[/embed-flash]');
bbtags[40] = '[hide]';
bbtags[41] = '[/hide]';
bbtags[42] = '[spoiler]';
bbtags[43] = '[/spoiler]';
bbtags[44] = '[wow]';
bbtags[45] = '[/wow]';
bbtags[46] = '[justify]';
bbtags[47] = '[/justify]';
bbtags[48] = '[sub]';
bbtags[49] = '[/sub]';
bbtags[50] = '[sup]';
bbtags[51] = '[/sup]';
bbtags[52] = '[left]';
bbtags[53] = '[/left]';
bbtags[54] = '[table]';
bbtags[55] = '[/table]';
bbtags[56] = '[hr]';
bbtags[58] = '[tr]';
bbtags[59] = '[/tr]';
bbtags[60] = '[td]';
bbtags[61] = '[/td]';

var selectId = new Array ('px','color','font','other','table_gui','flash','url','img','servimg_upload_gui','video', 'sel_smilies','dices','wpx','wcolor','wfont','wother','wtable_gui','wflash','wurl','wimg','wservimg_upload_gui','wvideo', 'wsel_smilies', 'wdices');

imageTag = false;
// Shows the help messages in the helpline window
function helpline(help) {
	// document.post.helpbox.value = eval(help + "_help");
	if ( help.length<5 )
	{
		document.getElementById('helpbox').innerHTML = eval(help + "_help");
	}
	else
	{
		document.getElementById('helpbox').innerHTML = help;
	}
}
// Replacement for arrayname.length property
function getarraysize(thearray) {
	for (i = 0; i < thearray.length; i++) {
		if ((thearray[i] == "undefined") || (thearray[i] == "") || (thearray[i] == null))
		return i;
	}
	return thearray.length;
}
// Replacement for arrayname.push(value) not implemented in IE until version 5.5
// Appends element to the array
function arraypush(thearray,value) {
	thearray[ getarraysize(thearray) ] = value;
}
// Replacement for arrayname.pop() not implemented in IE until version 5.5
// Removes and returns the last element of an array
function arraypop(thearray) {
	thearraysize = getarraysize(thearray);
	retval = thearray[thearraysize - 1];
	delete thearray[thearraysize - 1];
	return retval;
}
function checkForm() {
	formErrors = false;
	if (document.post.message.value.length < 3) {
		formErrors = "Vous devez entrer un message avant de poster.";
	}
	if (formErrors) {
		return false;
	} else {
		bbstyle(-1);
		//formObj.preview.disabled = true;
		//formObj.submit.disabled = true;
		return true;
	}
}
function emoticon(text) {
	var txtarea = document.post.message;
	text = ' ' + text + ' ';
	if (txtarea.createTextRange && txtarea.caretPos) {
		var caretPos = txtarea.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? caretPos.text + text + ' ' : caretPos.text + text;
		txtarea.focus();
	} else {
		txtarea.value += text;
		txtarea.focus();
	}
}
// Emoticons : Frame
function emoticonp(text) {
	if( parent.document.getElementById('html_edit') && parent.smilieoptions && parent.document.getElementById('html_edit').style.display!='none' )
	{
		var smiles = parent.smilieoptions;
		for (var i in smiles)
		{
			if ( smiles[i][2] == text )
			{
				var text = '<img src="' + smiles[i][0] + '" border="0" smilieid="' + i + '" />';
				text = '&nbsp;' + text + '&nbsp;';
				parent.vB_Editor['text_editor'].insert_text(text, false);
			}
		}
	}
	else
	{
		text = ' ' + text + ' ';
		if (parent.document.forms['post'].message.createTextRange && parent.document.forms['post'].message.caretPos) {
			var caretPos = parent.document.forms['post'].message.caretPos;
			caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
			parent.document.forms['post'].message.focus();
		} else {
			parent.document.forms['post'].message.value += text;
			parent.document.forms['post'].message.focus();
		}
	}
}
// Emoticons : Window
function emoticonw(text) {
	text = ' ' + text + ' ';
	if (opener.document.forms['post'].message.createTextRange && opener.document.forms['post'].message.caretPos) {
		var caretPos = opener.document.forms['post'].message.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
		opener.document.forms['post'].message.focus();
	} else {
		opener.document.forms['post'].message.value += text;
		opener.document.forms['post'].message.focus();
	}
}
function constructBBcode(bbcode,args,content) {
	var textarea = document.post.message;
	var i = 0;
	var tmp_args = '';
	var tmp_content = '';

	if (bbcode == 'flash' || bbcode == 'img' || (bbcode== 'url' && document.getElementById(content).value != '')) {
		tmp_content += document.getElementById(content).value;
	}
	else {
		tmp_content += document.getElementById(args[0]).value;
	}

	if (bbcode == 'flash' ) {
		if ( document.getElementById(args[0]).value > 0 && document.getElementById(args[1]).value > 0 ) {
			tmp_args += '(' + document.getElementById(args[0]).value + ',' + document.getElementById(args[1]).value + ')';
		}
	}
	else
	{
		if (bbcode == 'url' && document.getElementById(args[0]).value != '') {
			tmp_args += '=';
			if ( document.getElementById(args[0]).value.indexOf('www.') == 0 )
			{
				document.getElementById(args[0]).value = 'http://' + document.getElementById(args[0]).value;
			}
		}
		while ( i < args.length ) {
			tmp_args += document.getElementById(args[i]).value;
			document.getElementById(args[i]).value = '';
			if ( i != args.length - 1 ) {
				tmp_args += ',';
			}
			i++;
		}
	}

	textarea.value = textarea.value + '[' + bbcode + tmp_args + ']' + tmp_content + '[/' + bbcode + ']';
	document.getElementById(content).value = '';
}
function BBcodeVideo(id) {
	var url = document.getElementById(id).value;
	var textarea = document.post.message;
	var span = document.getElementById('inv_url');

	if ( url.indexOf('youtube') != '-1' ) {
		textarea.value = textarea.value + '[youtube]' + url + '[/youtube]';
		selectWysiwyg(this, 'video');
	}
	else if ( url.indexOf('dailymotion') != '-1') {
		textarea.value = textarea.value + '[dailymotion]' + url + '[/dailymotion]';
		selectWysiwyg(this, 'video');
	}
	else if ( url.indexOf('google') != '-1') {
		textarea.value = textarea.value + '[googlevideo]' + url + '[/googlevideo]';
		selectWysiwyg(this, 'video');
	}
	else {
		span.innerHTML = "L\'URL fournie est invalide:";
	}
}
function bbfontstyle(bbopen, bbclose) {
	var txtarea = document.post.message;
	if ((clientVer >= 4) && is_ie && is_win) {
		theSelection = document.selection.createRange().text;
		if (!theSelection) {
			txtarea.value += bbopen + bbclose;
			txtarea.focus();
			return;
		}
		document.selection.createRange().text = bbopen + theSelection + bbclose;
		txtarea.focus();
		return;
	}
	else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
	{
		mozWrap(txtarea, bbopen, bbclose);
		return;
	}
	else
	{
		txtarea.value += bbopen + bbclose;
		txtarea.setSelectionRange(txtarea.value.length - bbclose.length, txtarea.value.length - bbclose.length);
		txtarea.focus();
	}
	storeCaret(txtarea);
}
function bbstyle(bbnumber) {
	var txtarea = document.post.message;
	var button = document.getElementById('addbbcode'+bbnumber);
	if (bbnumber != -1) {
		var tag = document.getElementById('addbbcode'+bbnumber).tagName;
	}
	donotinsert = false;
	theSelection = false;
	bblast = 0;
	if (bbnumber == -1) { // Close all open tags & default button names
		while (bbcode[0]) {
			butnumber = arraypop(bbcode) - 1;
			txtarea.value += bbtags[butnumber + 1];
			var tag = document.getElementById('addbbcode'+butnumber).tagName;
			if ( tag == 'INPUT' ) {
				buttext = eval('document.post.addbbcode' + butnumber + '.value');
				eval('document.post.addbbcode' + butnumber + '.value ="' + buttext.substr(0,(buttext.length - 1)) + '"');
			}
			else if ( tag == 'BUTTON' && document.getElementById('addbbcode'+butnumber)){
				document.getElementById('addbbcode'+butnumber).className = 'button2';
			}
		}
		imageTag = false; // All tags are closed including image tags :D
		txtarea.focus();
		return;
	}
	if ((clientVer >= 4) && is_ie && is_win)
	{
		theSelection = document.selection.createRange().text; // Get text selection
		if (theSelection) {
			// Add tags around selection
			document.selection.createRange().text = bbtags[bbnumber] + theSelection + bbtags[bbnumber+1];
			txtarea.focus();
			theSelection = '';
			return;
		}
	}
	else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
	{
		mozWrap(txtarea, bbtags[bbnumber], bbtags[bbnumber+1]);
		return;
	}
	// Find last occurance of an open tag the same as the one just clicked
	for (i = 0; i < bbcode.length; i++) {
		if (bbcode[i] == bbnumber+1) {
			bblast = i;
			donotinsert = true;
		}
	}
	if (donotinsert) {		// Close all open tags up to the one just clicked & default button names
		while (bbcode[bblast]) {
			butnumber = arraypop(bbcode) - 1;
			txtarea.value += bbtags[butnumber + 1];
			if ( tag == 'INPUT' ) {
				buttext = eval('document.post.addbbcode' + butnumber + '.value');
				eval('document.post.addbbcode' + butnumber + '.value ="' + buttext.substr(0,(buttext.length - 1)) + '"');
			}
			else if (tag == 'BUTTON' && document.getElementById('addbbcode'+bbnumber)){
				button.className = 'button2';
			}
			imageTag = false;
		}
		txtarea.focus();
		return;
	} else { // Open tags
		if (imageTag && (bbnumber != 14)) {		// Close image tag before adding another
			txtarea.value += bbtags[15];
			lastValue = arraypop(bbcode) - 1;	// Remove the close image tag from the list
			document.post.addbbcode14.value = "Img";	// Return button back to normal state
			imageTag = false;
		}
		// Open tag
		txtarea.value += bbtags[bbnumber];
		if ((bbnumber == 14) && (imageTag == false)) imageTag = 1; // Check to stop additional tags after an unclosed image tag
		arraypush(bbcode,bbnumber+1);
		if ( tag == 'INPUT' ) {
			eval('document.post.addbbcode'+bbnumber+'.value += "*"');
		}
		else if (tag == 'BUTTON' && document.getElementById('addbbcode'+bbnumber)){
			button.className = 'button2 bbcode';
		}
		txtarea.focus();
		return;
	}
	storeCaret(txtarea);
}
function FindXY(obj){
	var x=0,y=0;
	while ( obj != null /*&& obj.id != 'main-content'*/ ){
		x+=obj.offsetLeft;
		y+=obj.offsetTop;
		obj=obj.offsetParent;
	}
	return {'x':x,'y':y};
}

function selectWysiwyg (button, div) {
	var div = document.getElementById(div);
	var visible = div.style.visibility;

	if ( visible == 'hidden' ) {
		var cd = FindXY(button);
		var h = button.offsetHeight;
		var i = 0;
		while ( i < selectId.length ) {
			if ( document.getElementById(selectId[i]) ) {
				document.getElementById(selectId[i]).style.visibility = 'hidden';
			}
			i++;
		}
		div.style.visibility = 'visible';
		if ( div.id != 'sel_smilies' && div.id != 'wsel_smilies' )
		{
			div.style.width = 'auto';
		}
		else
		{
			if ( !document.getElementById('sel_smilies_content') )
			{
				var smilies_content = '<div id="sel_smilies_content" align="center">';
				for (var smilieid in smilieoptions)
				{
					smilies_content += '<button onclick="emoticonp(\'' + smilieoptions[smilieid][2] + '\');selectWysiwyg(this, \'sel_smilies\');return false;"><img alt="' + smilieoptions[smilieid][1] + '" title="' + smilieoptions[smilieid][1] + '" src="' + smilieoptions[smilieid][0] + '" /></button> ';
				}
				smilies_content += '</div>';
				div.innerHTML = smilies_content;
			}
		}

		overFlowX = cd['x'] + div.offsetWidth - document.body.offsetWidth;
		cd['x'] = overFlowX > 0 ? cd['x'] - overFlowX : cd['x'];

		div.style.left = cd['x']+'px';
		div.style.top = (cd['y']+h)+'px';
	}
	else {
		div.style.visibility = 'hidden';
	}
}
// From http://www.massless.org/mozedit/
function mozWrap(txtarea, open, close)
{
	var selLength = txtarea.textLength;
	var selStart = txtarea.selectionStart;
	var selEnd = txtarea.selectionEnd;
	if (selEnd == 1 || selEnd == 2)
	selEnd = selLength;
	var s1 = (txtarea.value).substring(0,selStart);
	var s2 = (txtarea.value).substring(selStart, selEnd)
	var s3 = (txtarea.value).substring(selEnd, selLength);
	txtarea.value = s1 + open + s2 + close + s3;
	return;
}
// Insert at Claret position. Code from
// http://www.faqts.com/knowledge_base/view.phtml/aid/1052/fid/130
function storeCaret(textEl) {
	if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}


// Direction de la page (ltr / rtl)
var html = document.getElementsByTagName('html');
var document_dir = 'ltr';
for(var i= 0; i < html.item(0).attributes.length; i++)
{
	var item = html.item(0).attributes[i];
	if(item.name == 'dir' && item.value == 'ltr' || item.value == 'rtl')
	{
		document_dir = item.value;
		break;
	}
}

// Position de la souris
var mouse_y = 0;
var mouse_x = 0;
function get_mouseX(evt)
{
	if (!evt)
	{
		evt = window.event;
	}

	if (evt.pageX)
	{
		return evt.pageX;
	}
	else if (evt.clientX)
	{
		var offset_temp = 0;
		if ( document.documentElement )
		{
			if ( document.documentElement.scrollLeft )
			{
				offset_temp = document.documentElement.scrollLeft;
			}
			else // En cas de changement de page avec scroll sur la prï¿½cï¿½dente
			{
				offset_temp = 0;
			}
		}
		else
		{
			offset_temp = document.body.scrollLeft;
		}
		return evt.clientX + offset_temp;
	}
	else
	{
		return 0;
	}
}
function get_mouseY(evt)
{
	if (!evt)
	{
		evt = window.event;
	}

	if (evt.pageY)
	{
		return evt.pageY;
	}
	else if (evt.clientY)
	{
		var offset_temp = 0;
		if ( document.documentElement )
		{
			if ( document.documentElement.scrollTop )
			{
				offset_temp = document.documentElement.scrollTop;
			}
			else // En cas de changement de page avec scroll sur la prï¿½cï¿½dente
			{
				offset_temp = 0;
			}
		}
		else
		{
			offset_temp = document.body.scrollTop;
		}
		return evt.clientY + offset_temp;
	}
	else
	{
		return 0;
	}
}
function get_mouse_pos(evt) {
	if (document.getElementById)
	{
		mouse_y = (parseInt(get_mouseY(evt))+15) + 'px';
		mouse_x = (parseInt(get_mouseX(evt))+15) + 'px';
	}
}
if (document.all) {
	document.attachEvent("onmousemove", get_mouse_pos);
}
else {
	document.addEventListener("mousemove", get_mouse_pos, true);
}

// Afficher/cacher les "div search" et "plus menu"
function showhide(vari) {
	var window_width = 0;
	if ( document.getElementById('content') )
	{
		window_width = document.getElementById('content').offsetWidth;
	}
	else
	{
		window_width = (document.body) ? document.body.clientWidth : window.innerWidth;
	}

	if ( vari != document.getElementById('plus_menu') )
	{
		// top
		vari.style.top = mouse_y;
		window_width = (document.body) ? document.body.clientWidth : window.innerWidth;
	}

	vari.style.display=(vari.style.display=="none") ? '' : 'none';
	var vari_style_width = parseInt(vari.style.width);
	vari_style_width = (!isNaN(vari_style_width)) ? vari_style_width : vari.offsetWidth;
	var element_vari = vari;
	while ( vari_style_width == 0 && element_vari.firstChild.offsetWidth )
	{
		element_vari = element_vari.firstChild;
		vari_style_width = element_vari.offsetWidth;
	}

	mouse_x = parseInt(mouse_x);
	while(vari_style_width + mouse_x >= window_width)
	{
		mouse_x -= 10;
	}
	vari.style.left = mouse_x + 'px';
}

function insert_search_menu(session_id) {
	session_input = (session_id ? '<input type="hidden" name="sid" value="'+session_id+'" />' : '');
	session_id = (session_id ? '?sid='+session_id : '');
	document.write('<div id="search_menu" style="display:none;position:absolute;z-index:10000"><form action="/search.forum" method="get"><input type="hidden" name="mode" value="searchbox" /><table class="forumline" cellpadding="3" cellspacing="0" border="0"><tr><th class="thHead">Rechercher</th></tr><tr><td class="row2" align="center"><input type="text" class="post" name="search_keywords" size="24" style="height:20px; margin-top:6px; margin-right:3px;" /> <input type="submit" class="button" value="Go" /></td></tr><tr><td class="row2" align="center" nowrap="nowrap"><span class="genmed">&nbsp;Résultats par :&nbsp; <input id="rposts" type="radio" name="show_results" value="posts" /><label for="rposts">&nbsp;Messages</label> <input id="rtopics" type="radio" name="show_results" value="topics" checked="checked" /><label for="rtopics">&nbsp;Sujets</label>&nbsp;</td></tr><tr><td class="row2" align="center"><span class="genmed"><hr><a href="/search.forum'+session_id+'" rel="nofollow"><img src="http://illiweb.com/fa/icon_mini_search.gif" width="12" height="13" border="0" hspace="3" />&nbsp;Recherche avancée</a><span></td></tr></table>'+session_input+'</form></div>');
}

function insert_search_menu_new(session_id) {
	session_input = (session_id ? '<input type="hidden" name="sid" value="'+session_id+'" />' : '');
	session_id = (session_id ? '?sid='+session_id : '');
	document.write('<div class="overview row3" id="search_menu" style="display:none;position:absolute;width:350px;z-index:10000"><form action="/search.forum" method="get"><input type="hidden" name="mode" value="searchbox" /><p class="title-overview row2">Rechercher</p><p class="center-overview"><input type="text" class="inputbox medium" name="search_keywords" />'+session_input+'<input type="submit" class="button1" value="Go" /><br />Résultats par : <label for="rposts"><input id="rposts" type="radio" name="show_results" value="posts" /> Messages</label> <label for="rtopics"><input id="rtopics" type="radio" name="show_results" value="topics" checked="checked" /> Sujets</label></p><hr class="dashed" /><p class="center-overview"><a href="/search.forum'+session_id+'" rel="nofollow"><img src="http://illiweb.com/fa/icon_mini_search.gif" width="12" height="13" alt="" /> Recherche avancée</a></p>'+session_input+'</form></div>');
}

function insert_plus_menu(search_where,session_id,add_favourite) {
	var favourite = '';
	if(add_favourite)
	{
		favourite = search_where.replace(new RegExp("f([0-9]*)(&|&amp;)t=([0-9]*)","g"), '$3');
		favourite = '<a href="/search.forum?search_id=favouritesearch&amp;add_favourite='+favourite+session_id+'">Ajouter à ses favoris</a><br />';
	}
	search_where = '&amp;search_where='+search_where;
	session_id = (session_id ? '&amp;sid='+session_id : '');
	document.write('<a href="javascript:showhide(document.getElementById(\'plus_menu\'))">Plus !</a><br /><div id="plus_menu" style="display:none;position:absolute;margin-top:8px;z-index:1;"><table class="forumline" cellpadding="3" cellspacing="0" border="0" width="200"><tr><th class="thHead">Plus !</th></tr><tr><td class="row1" nowrap="nowrap"><span class="gensmall"><b><a href="/search.forum?search_id=newposts'+search_where+session_id+'">Voir les nouveaux messages depuis votre dernière visite</a><br /><a href="/search.forum?search_id=egosearch'+search_where+session_id+'">Voir ses messages</a><br /><a href="/search.forum?search_id=unanswered'+search_where+session_id+'">Voir les messages sans réponses</a><br /><a href="/search.forum?search_id=watchsearch'+search_where+session_id+'">Sujets surveillés</a><hr>'+favourite+'<a href="/tell_friend.forum?f='+search_where+session_id+'">Envoyer à un ami</a><br /><a href="'+self.location.href+'" onclick="link_bbcode();return false">Copier l\'adresse BBCode de la page</a><br /><a href="javascript:void(0);" onclick="window.print();return false">Imprimer cette page</a></b></span></td></tr></table></div>');
}

function insert_plus_menu_new(search_where,session_id,add_favourite,watch_topic) {
	var watch = '';
	if (watch_topic)
	{
		watch = watch_topic + '<br />';
	}
	var favourite = '';
	if(add_favourite)
	{
		favourite = search_where.replace(new RegExp("f([0-9]*)(&|&amp;)t=([0-9]*)","g"), '$3');
		favourite = '<a href="/search.forum?search_id=favouritesearch&amp;add_favourite='+favourite+session_id+'">Ajouter à ses favoris</a><br />';
	}
	search_where = '&amp;search_where='+search_where;
	session_id = (session_id ? '&amp;sid='+session_id : '');
	document.write('<a href="javascript:showhide(document.getElementById(\'plus_menu\'))">Plus !</a><br /><div class="overview row3" id="plus_menu" style="display:none;position:absolute;width:400px;margin-top:8px;z-index:1;"><p class="title-overview row2"><strong>Plus !</strong></p><p class="left-overview"><strong><a href="/search.forum?search_id=newposts'+search_where+session_id+'">Voir les nouveaux messages depuis votre dernière visite</a><br /><a href="/search.forum?search_id=egosearch'+search_where+session_id+'">Voir ses messages</a><br /><a href="/search.forum?search_id=unanswered'+search_where+session_id+'">Voir les messages sans réponses</a><br /><a href="/search.forum?search_id=watchsearch'+search_where+session_id+'">Sujets surveillés</a></strong></p><hr class="dashed" /><p class="left-overview"><strong>'+watch+favourite+'<a href="/tell_friend.forum?f='+search_where+session_id+'">Envoyer à un ami</a><br /><a href="'+self.location.href+'" onclick="link_bbcode();return false">Copier l\'adresse BBCode de la page</a><br /><a href="javascript:void(0);" onclick="window.print();return false">Imprimer cette page</a></strong></p></div>');
}

function insert_plus_album(search_where,session_id) {
	session_id = (session_id ? '&amp;sid='+session_id : '');
	document.write('<a href="javascript:showhide(document.getElementById(\'plus_menu\'))">Plus !</a><br /><div id="plus_menu" style="display:none;position:absolute;right:100px;z-index:1;"><table class="forumline" cellpadding="3" cellspacing="0" border="0"><tr><th class="thHead">Plus !</th></tr><tr><td class="row1" nowrap="nowrap"><span class="gensmall"><b><a href="/tell_friend.forum?album='+search_where+session_id+'">Envoyer à un ami</a></b></span></td></tr></table></div>');
}

function insert_plus_album_new(search_where,session_id) {
	session_id = (session_id ? '&amp;sid='+session_id : '');
	document.write('<a href="javascript:showhide(document.getElementById(\'plus_menu\'))">Plus !</a><br /><div class="overview row3" id="plus_menu" style="display:none;margin: 8px 20px 0px 0px;position:absolute;right:20px;width:200px;z-index:1;"><p class="title-overview row2">Plus !</p><p class="left-overview"><strong><a href="/tell_friend.forum?album='+search_where+session_id+'">Envoyer à un ami</a></strong></p></div>');
}

function insert_plus_pic(search_where,session_id) {
	session_id = (session_id ? '&amp;sid='+session_id : '');
	document.write('<a href="javascript:showhide(document.getElementById(\'plus_menu\'))">Plus !</a><br /><div id="plus_menu" style="display:none;position:absolute;right:100px;z-index:1;"><table class="forumline" cellpadding="3" cellspacing="0" border="0"><tr><th class="thHead">Plus !</th></tr><tr><td class="row1" nowrap="nowrap"><span class="gensmall"><b><a href="/tell_friend.forum?pic='+search_where+session_id+'">Envoyer à un ami</a></b></span></td></tr></table></div>');
}

function insert_plus_pic_new(search_where,session_id) {
	session_id = (session_id ? '&amp;sid='+session_id : '');
	document.write('<a href="javascript:showhide(document.getElementById(\'plus_menu\'))">Plus !</a><br /><div class="overview row3" id="plus_menu" style="display:none;position:absolute;right:20px;margin-top:20px;z-index:1;"><p class="title-overview row2">Plus !</p><p class="left-overview"><strong><a href="/tell_friend.forum?pic='+search_where+session_id+'">Envoyer à un ami</a></strong></p></div>');
}

function link_bbcode() {
	intext = "[url="+self.location.href+"]"+window.document.title+"[/url]";
	if(document.all && !window.opera)
	{
		window.clipboardData.setData('Text', intext);
	}
	else
	{
		prompt('',intext);
	}
}

function ShowHideLayer(layer_open, layer_close) {
	if (layer_open != '') {
		expandLayer(layer_open);
	}
	if (layer_close != '') {
		expandLayer(layer_close);
	}
}

function ShowHideMenu(layer_open, layer_close, page_id, new_class) {
	if (layer_open != '') {
		expandLayer(layer_open);
	}

	if (layer_close != '') {
		expandLayer(layer_close);
	}

	if (document.getElementById(page_id).className == new_class)
	{
		document.getElementById(page_id).className = '';
	}
	else
	{
		document.getElementById(page_id).className = new_class;
	}

}

function expandLayer(name) {
	var itm = null;

	if (document.getElementById) {
		itm = document.getElementById(name);
	} else if (document.all) {
		itm = document.all[name];
	} else if (document.layers) {
		itm = document.layers[name];
	}

	if (!itm) {
		// Just don't panik, it's ok
	} else if (itm.style) {
		if (itm.style.display == "none") {
			itm.style.display = "";
		} else {
			itm.style.display = "none";
		}
	} else {
		itm.visibility = "show";
	}
}

function fa_endpage() {
	if (parent.wbo1_ferme) wbo1_ferme();
	//if (parent.slide_close) slide_close();
}

function hdr_ref(object)
{
	if (document.getElementById)
	{
		return document.getElementById(object);
	}
	else if (document.all)
	{
		return eval('document.all.' + object);
	}
	else
	{
		return false;
	}
}

function hdr_expand(object)
{
	var object = hdr_ref(object);

	if( !object.style )
	{
		return false;
	}
	else
	{
		object.style.display = '';
	}

	if (window.event)
	{
		window.event.cancelBubble = true;
	}
}

function hdr_contract(object)
{
	var object = hdr_ref(object);

	if( !object.style )
	{
		return false;
	}
	else
	{
		object.style.display = 'none';
	}

	if (window.event)
	{
		window.event.cancelBubble = true;
	}
}

function hdr_toggle(object, open_close, open_icon, close_icon)
{
	var object = hdr_ref(object);
	var icone = hdr_ref(open_close);

	if( !object.style )
	{
		return false;
	}

	if( object.style.display == 'none' )
	{
		object.style.display = '';
		icone.src = close_icon;
	}
	else
	{
		object.style.display = 'none';
		icone.src = open_icon;
	}
}

function select_switch_col(nomchamp) {
	for (i=0; i<document.post.length; i++)
	{
		if (document.post.elements[i].name && (document.post.elements[i].name).substring(0,nomchamp.length) == nomchamp && document.post.elements[i].disabled != true)
		{
			document.post.elements[i].checked = !document.post.elements[i].checked;
		}
	}
}


function disabled1(choix,nomchamp) {

	var formulaire = document.getElementById(choix);
	/*
	if ( formulaire.selectedIndex != 2 )
	{
	for (i=0; i<document.post.length; i++)
	{
	if ( (document.post.elements[i].name).substring(0,nomchamp.length) == nomchamp)
	{
	document.post.elements[i].disabled = 'disabled';
	}
	}
	}
	if ( formulaire.selectedIndex == 2 )
	{
	for (i=0; i<document.post.length; i++)
	{
	if ( (document.post.elements[i].name).substring(0,nomchamp.length) == nomchamp)
	{
	document.post.elements[i].disabled = '';
	}
	}
	}
	*/
	for (i=0; i<document.post.length; i++)
	{
		if ( document.post.elements[i].type=='checkbox' && (document.post.elements[i].name).substring(0,nomchamp.length) == nomchamp)
		{
			document.post.elements[i].disabled = ((formulaire.selectedIndex != 2)?'disabled':'');
		}
	}

}

function disabled2(choix,nomchamp) {

	var formulaire = document.getElementById(choix);
	/*
	if ( formulaire.selectedIndex != 1 )
	{
	for (i=0; i<document.post.length; i++)
	{
	if ( (document.post.elements[i].name).substring(0,nomchamp.length) == nomchamp)
	{
	document.post.elements[i].disabled = 'disabled';
	}
	}
	}
	if ( formulaire.selectedIndex == 1 )
	{
	for (i=0; i<document.post.length; i++)
	{
	if ( (document.post.elements[i].name).substring(0,nomchamp.length) == nomchamp)
	{
	document.post.elements[i].disabled = '';
	}
	}
	}
	*/
	for (i=0; i<document.post.length; i++)
	{
		if ( document.post.elements[i].type=='checkbox' && (document.post.elements[i].name).substring(0,nomchamp.length) == nomchamp)
		{
			document.post.elements[i].disabled = ((formulaire.selectedIndex != 1)?'disabled':'');
		}
	}

}

var agt = navigator.userAgent.toLowerCase();
var originalFirstChild;

function createTitle(which, string, x, y)
{
	if (typeof(originalFirstChild) == 'undefined')
	{
		originalFirstChild = document.body.firstChild;
	}

	x = parseInt(mouse_x);
	y = parseInt(mouse_y);

	element = document.createElement('div');
	element.style.position = 'absolute';
	element.style.zIndex = 1000;
	element.style.visibility = 'hidden';
	excessWidth = 0;
	if (document.all)
	{
		excessWidth = 50;
	}
	excessHeight = 20;
	element.innerHTML = '<div class="bodyline" style="max-width:400px;"><table cellspacing="0" cellpadding="0" border="0"><tr><td><span class="gen">' + string + '</span></td></tr></table></div>';
	renderedElement = document.body.insertBefore(element, document.body.firstChild);
	renderedWidth = renderedElement.offsetWidth;
	renderedHeight = renderedElement.offsetHeight;

	renderedElement.style.top = (y + 10) + 'px';
	renderedElement.style.left = (x + 10) + 'px';
	var window_width = document.getElementById('content') ? document.getElementById('content').offsetWidth : ( (document.body) ? document.body.clientWidth : window.innerWidth );
	while(parseInt(renderedElement.style.left) + renderedElement.offsetWidth >= window_width)
	{
		renderedElement.style.left = (parseInt(renderedElement.style.left)-10)+'px';
	}

	if (agt.indexOf('gecko') != -1 && agt.indexOf('win') != -1)
	{
		setTimeout("renderedElement.style.visibility = 'visible'", 1);
	}
	else
	{
		renderedElement.style.visibility = 'visible';
	}
}

function destroyTitle()
{
	if (document.body.firstChild != originalFirstChild)
	{
		document.body.removeChild(document.body.firstChild);
	}
}

function my_getcookie( name )
{
	cname = name + '=';
	cpos = document.cookie.indexOf( cname );

	if ( cpos != -1 )
	{
		cstart = cpos + cname.length;
		cend = document.cookie.indexOf(";", cstart);

		if (cend == -1)
		{
			cend = document.cookie.length;
		}

		return unescape( document.cookie.substring(cstart, cend) );
	}

	return null;
}

function my_setcookie( name, value, sticky, path )
{
	expires = "";
	domain = "";

	if ( sticky )
	{
		expires = "; expires=Wed, 1 Jan 2020 00:00:00 GMT";
	}

	if ( ! path )
	{
		path = "/";
	}

	document.cookie = name + "=" + value + "; path=" + path + expires + domain + ';';
}

function expandAllLayer(name, open_close, layer_open_close) {
	var itm = null;

	if (document.getElementById) {
		itm = document.getElementById(name);
	} else if (document.all) {
		itm = document.all[name];
	} else if (document.layers) {
		itm = document.layers[name];
	}

	if (!itm) {
	} else if (itm.style) {
		if (itm.style.display == "none")
		{
			if( (open_close == "open" && layer_open_close=="open") || (open_close == "close" && layer_open_close=="close") )
			{
				itm.style.display = "";
			}
		}
		else
		{
			if( (open_close == "close" && layer_open_close=="open") || (open_close == "open" && layer_open_close=="close") )
			{
				itm.style.display = "none";
			}
		}
	}
	else
	{
		itm.visibility = "show";
	}

}

function check(action,formname)
{
	var formnamevalue = document.forms[arguments[1]];

	field = formnamevalue.elements.length;
	switch(action)
	{	case "select":	for (i = 0; i < field; i++) {
		formnamevalue.elements[i].checked = true;
	}
	break;
	case "unselect":	for (i = 0; i < field; i++) {
		formnamevalue.elements[i].checked = false;
	}
	break;
	}
}

function refresh_username(selected_username)
{
	if ( (opener.document.forms['post'].username.value) && (opener.document.forms['post'].ismp) )
	{
		opener.document.forms['post'].username.value = opener.document.forms['post'].username.value + ';' + selected_username;
	}
	else
	{
		opener.document.forms['post'].username.value = selected_username;
	}
	opener.focus();
	window.close();
}

function refresh_username_new(username, fieldname)
{
	$('input[name^=' + ( fieldname || 'username' ) + ']:last').val(username);
	if ( $.add_username )
	{
		$.add_username();
	}
}

function timestamp()
{
	return Math.floor((new Date()).getTime() / 1000);
}

function insertChatBox(chatbox_id, chatbox_url)
{
	document.getElementById(chatbox_id).innerHTML = '<iframe src="' + chatbox_url + '" id="frame_chatbox" scrolling="no" width="100%" height="100%" marginwidth="0" marginheight="0" frameborder="0"></iframe>';
}

function insertChatBoxNew(chatbox_id, chatbox_url)
{
	document.getElementById(chatbox_id).innerHTML = '<object data="' + chatbox_url + '" id="frame_chatbox" scrolling="yes" width="100%" height="100%" type="text/html"></object>';
}

function insertChatBoxPopup(chatbox_url, l_chatbox_join)
{
	document.getElementById('chatbox_popup').innerHTML = '[ <a href="' + chatbox_url + '" target="ChatBox">' + l_chatbox_join + '</a> ]';
}


/****************************************************
* Function that show a context menu for the chatbox	*
* date : 22/12/2006									*
* author : Vincent									*
****************************************************/
function showMenu (user_id, user_name, my_user_id, my_chat_level, my_user_level, user_chat_level, user_level, event, sid) {
	/* if the context menu is already shown or the user is not connected to the chatbox */
	if (document.getElementById('chatbox_contextmenu'))
	{
		hideMenu();
		return false;
	}

	var connected = false;
	if (document.forms[0].elements["message"])
	{
		connected = true;
	}

	/* ------ get mouse info for displaying the menu -------- */
	if (document.all) {
		mouseX = window.event.clientX + document.body.scrollLeft;
		mouseY = window.event.clientY + document.body.scrollTop;
	}
	else {
		mouseX = event.clientX + window.scrollX;
		mouseY = event.clientY + window.scrollY;
	}

	//if (mouseX > 50)
	//mouseX = 50;

	/* ---------- create the div of the menu ------------- */
	var div = document.createElement('div');
	div.setAttribute('id', 'chatbox_contextmenu');
	div.style.display = 'block';
	div.style.top = mouseY+'px';
	//div.style.left = mouseX+'px';

	// Dï¿½but Ajout
	var window_width = 0;
	if ( document.getElementById('content') )
	{
		window_width = document.getElementById('content').offsetWidth;
	}
	else
	{
		window_width = (document.body) ? document.body.clientWidth : window.innerWidth;
	}

	var div_style_width = 120;

	mouseX = parseInt(mouseX);
	while(div_style_width + mouseX >= window_width)
	{
		mouseX -= 10;
	}
	div.style.left = mouseX + 'px';
	// Fin Ajout

	div.style.position = 'absolute';
	//div.style.width = '10em';

	/* ------- create the title of the menu with close button --------- */
	var p = document.createElement('p');
	//p.style.textAlign = 'left';
	p.setAttribute('class', 'close');
	p.setAttribute('className', 'close');
	var title_name = ' ' + ((user_name.length > 9) ? user_name.substr(0,9)+'...' : user_name);

	var close = document.createElement('img');
	close.onclick = new Function ('hideMenu();');
	close.setAttribute('src', 'http://illiweb.com/fa/cross.png');
	close.setAttribute('alt', 'Fermer la fenêtre');
	//close.setAttribute('class', 'right');

	p.appendChild(document.createTextNode(title_name));
	p.appendChild(close);

	div.appendChild(p);

	/* --------- create the see profile link ------------- */
	var p = document.createElement('p');
	p.onmouseover = new Function ('this.className="hover";');
	p.onmouseout = new Function ('this.className="";');
	var link = document.createElement('a');
	link.appendChild(document.createTextNode("Voir le profil"));
	link.setAttribute('href', '/profile.forum?mode=viewprofile&u='+user_id+(sid=='' ? '' : '&sid='+sid));
	link.setAttribute('target', 'profile');
	link.onclick = new Function ("hideMenu();");

	p.appendChild(link);
	div.appendChild(p);

	/* --------- create the send pm link ------------- */
	var p = document.createElement('p');
	p.onmouseover = new Function ('this.className="hover";');
	p.onmouseout = new Function ('this.className="";');
	var link = document.createElement('a');
	link.appendChild(document.createTextNode("Envoyer un MP"));
	link.setAttribute('href', '/msg.forum?mode=post&u='+user_id+(sid=='' ? '' : '&sid='+sid));
	link.setAttribute('target', 'profile');
	link.onclick = new Function ("hideMenu();");

	p.appendChild(link);
	div.appendChild(p);

	/* --------- create the ban user link ------------- */
	if (document.forms[0].elements["message"] && my_chat_level == 2)
	{
		user_name = user_name.replace(/\\/g, "\\\\");
		user_name = user_name.replace(/\'/g, "\\'" );

		if (user_chat_level != 2)
		{
			/* -- Kick -- */
			var p = document.createElement('p');
			p.onmouseover = new Function ('this.className="hover";');
			p.onmouseout = new Function ('this.className="";');
			var link = document.createElement('a');
			link.appendChild(document.createTextNode("Kicker du chat"));
			link.setAttribute('href', 'javascript:void(0)');
			link.onclick = new Function ("return action_user('kick', '"+user_name+"', '"+sid+"');");
			p.appendChild(link);
			div.appendChild(p);

			/* -- Ban -- */
			var p = document.createElement('p');
			p.onmouseover = new Function ('this.className="hover";');
			p.onmouseout = new Function ('this.className="";');
			var link = document.createElement('a');
			link.appendChild(document.createTextNode("Bannir du chat"));
			link.setAttribute('href', 'javascript:void(0)');
			link.onclick = new Function ("return action_user('ban','"+user_name+"', '"+sid+"');");
		}

		p.appendChild(link);
		div.appendChild(p);

		if (my_user_level == 1 && user_chat_level == 2 && user_level != 1 )
		{
			var p = document.createElement('p');
			p.onmouseover = new Function ('this.className="hover";');
			p.onmouseout = new Function ('this.className="";');
			var link = document.createElement('a');
			link.appendChild(document.createTextNode("Retirer modération"));
			link.setAttribute('href', 'javascript:void(0)');
			link.onclick = new Function ("return action_user('unmod','"+user_name+"', '"+sid+"');");

			p.appendChild(link);
			div.appendChild(p);
		}
		else if (my_user_level == 1 && user_chat_level != 2 )
		{
			var p = document.createElement('p');
			p.onmouseover = new Function ('this.className="hover";');
			p.onmouseout = new Function ('this.className="";');
			var link = document.createElement('a');
			link.appendChild(document.createTextNode("Ajout modérateur"));
			link.setAttribute('href', 'javascript:void(0)');
			link.onclick = new Function ("return action_user('mod','"+user_name+"', '"+sid+"');");

			p.appendChild(link);
			div.appendChild(p);
		}
	}

	if (connected && user_id == my_user_id)
	{
		var p = document.createElement('p');
		p.onmouseover = new Function ('this.className="hover";');
		p.onmouseout = new Function ('this.className="";');
		var link = document.createElement('a');
		link.appendChild(document.createTextNode("S\'absenter"));
		link.setAttribute('href', 'javascript:void(0)');
		link.onclick = new Function ("return action_user('away', prompt('Raison',''), '"+sid+"');");

		p.appendChild(link);
		div.appendChild(p);

		var p = document.createElement('p');
		p.onmouseover = new Function ('this.className="hover";');
		p.onmouseout = new Function ('this.className="";');
		var link = document.createElement('a');
		link.appendChild(document.createTextNode("Quitter"));
		link.setAttribute('href', 'javascript:void(0)');
//		link.onclick = new Function ("return action_user('exit', prompt('Raison',''), '"+sid+"');");
		link.onclick = new Function ("hideMenu();return CB_disconnect();");

		p.appendChild(link);
		div.appendChild(p);
	}

	document.body.appendChild(div);
	return false;
}

function action_user (cmd, user_name, sid)
{
	if (user_name == null) user_name = '';
	document.forms[0].elements["message"].value = '/' + cmd + ' ' + user_name;
	submitmsg( params );
	hideMenu ();
	return false;
}


/* only delete the contextmenu from the document */
function hideMenu ()
{
	document.getElementById('chatbox_contextmenu').parentNode.removeChild(document.getElementById('chatbox_contextmenu'));
}

function js_urlencode(text)
{
	text = text.toString();

	// this escapes 128 - 255, as JS uses the unicode code points for them.
	// This causes problems with submitting text via AJAX with the UTF-8 charset.
	var matches = text.match(/[\x90-\xFF]/g);
	if (matches)
	{
		for (var matchid = 0; matchid < matches.length; matchid++)
		{
			var char_code = matches[matchid].charCodeAt(0);
			text = text.replace(matches[matchid], '%u00' + (char_code & 0xFF).toString(16).toUpperCase());
		}
	}

	return escape(text).replace(/\+/g, "%2B");
}

function ajax_refresh_chatbox(params)
{
	if (window.XMLHttpRequest)
	{
		// Mozilla, Safari, ...
		var http_request = new XMLHttpRequest();
	} else if (window.ActiveXObject)
	{
		// IE
		var http_request = new ActiveXObject("Microsoft.XMLHTTP");
	}

	http_request.onreadystatechange = function()
	{
		if (http_request.readyState == 4 && http_request.status == 200)
		{
			// destroyer
			var parsed_text = http_request.responseText;

			// only connected
			if ( parent.frames['ekran'].document.getElementById('message_rows') )
			{
				parent.frames['ekran'].document.getElementById('message_rows').innerHTML = parsed_text;
				parent.frames['ekran'].window.scrollTo(0,99999);
				ajax_refresh_chatterlist(params);
			}

			if( parsed_text.indexOf('<script>/*stop_refresh*/</script>',0) != -1 )
			{
				clearInterval(parent.frames['ekran'].Interval);
				parent.frames['ekran'].refreshing = false;

				var title_params = (params == '') ? '?page=front' : params + '&page=front';
				parent.frames['title'].location.href 	= '/chatbox/chatbox_title.forum' + title_params;
				parent.frames['sender'].location.href 	= '/chatbox/messenger_send.forum' + title_params;
			}
			else
			{
				if ( parent.frames['ekran'].refreshing == false )
				{
					parent.frames['ekran'].Interval = setInterval('parent.frames[\'ekran\'].refresh_chatbox()', 8000);
					parent.frames['ekran'].refreshing = true;
				}
			}
		}
	};

	http_request.open('GET', '/chatbox/generate_messages.forum' + params , true);
	http_request.send(null);
}

function ajax_submit_chatbox(params)
{
	var data = '&nick=' + js_urlencode(document.post.nick.value);
	data += '&sent=' + js_urlencode(document.post.sent.value);
	data += '&sbold=' + document.post.sbold.value;
	data += '&sitalic=' + document.post.sitalic.value;
	data += '&sunderline=' + document.post.sunderline.value;
	data += '&sstrike=' + document.post.sstrike.value;
	data += '&scolor=' + document.post.scolor.value;

	if( document.post.sent.value == '/banlist' )
	{
		window.open('/chatbox/chatbox_banlist.forum' + params, 'color','toolbar=no,menubar=no,personalbar=no,width=450,height=300,scrollbars=no,resizable=yes');
		return false;
	}

	if (window.XMLHttpRequest)
	{
		// Mozilla, Safari, ...
		var http_request_submit = new XMLHttpRequest();
	} else if (window.ActiveXObject)
	{
		// IE
		var http_request_submit = new ActiveXObject("Microsoft.XMLHTTP");
	}

	http_request_submit.onreadystatechange = function()
	{
		if (http_request_submit.readyState == 4 && http_request_submit.status == 200)
		{
			// destroyer
			/*var parsed_text = http_request_submit.responseText;

			parent.frames['ekran'].document.getElementById('message_rows').innerHTML = parsed_text;
			parent.frames['ekran'].window.scrollTo(0,99999);*/
			ajax_refresh_chatbox(params);
			ajax_refresh_chatterlist(params);
		}
	};

	http_request_submit.open('POST', '/chatbox/generate_messages.forum' + params, true);
	http_request_submit.setRequestHeader('Content-Type','application/x-www-form-urlencoded;');
	http_request_submit.send(data);
}

function ajax_refresh_chatterlist(params)
{
	if (window.XMLHttpRequest)
	{
		// Mozilla, Safari, ...
		var http_request_list = new XMLHttpRequest();
	} else if (window.ActiveXObject)
	{
		// IE
		var http_request_list = new ActiveXObject("Microsoft.XMLHTTP");
	}

	http_request_list.onreadystatechange = function()
	{
		if (http_request_list.readyState == 4 && http_request_list.status == 200)
		{
			// destroyer
			var parsed_text = http_request_list.responseText;

			if (parent.frames['who'].document.getElementById('chatter_rows'))
			{
				parent.frames['who'].document.getElementById('chatter_rows').innerHTML = parsed_text;
			}
		}
	};

	http_request_list.open('GET', '/chatbox/generate_chatterlist.forum' + params , true);
	http_request_list.send(null);
}

function insert_chatboxsmilie(smilie_code)
{
	opener.document.getElementById('message').value = opener.document.getElementById('message').value + smilie_code;
	opener.document.post.message.focus();
	window.close();
}


function change_display_by_icon(element,element_id,content_more,content_less,display_special)
{
	element.className=(element.className=="icon_less")?"icon_more":"icon_less";
	element.style.background ='url(\''+ ((element.className=="icon_less")?'http://illiweb.com/fa/i/tabs_less.gif':'http://illiweb.com/fa/i/tabs_more.gif') + '\') no-repeat';
	if(content_more || content_less)
	{
		element.innerHTML=(element.className=="icon_less")?content_less:content_more;
	}
	if(!display_special)
	{
		display_special = 'block';
	}
	document.getElementById(element_id).style.display = ((element.className=="icon_more")?'none':display_special);

	my_setcookie('display_sql_info', element.className);
}

function switchuploadaddress(file) {
	if (file) {
		document.getElementById('upfile').style.display='inline';
		document.getElementById('upurl').style.display='none';
	}
	else {
		document.getElementById('upfile').style.display='none';
		document.getElementById('upurl').style.display='inline';
	}
}

function do_mark(mode, type)
{
	if ( type == 2 )
	{
		if ( mode == 7 )
		{
			for (i = 0; i < form.elements["mark[]"].length; ++i)
			{
				radio_box = form.elements["mark[]"][i];
				if (radio_box.checked == true)
				{
					radio_box.checked = false;
				}
				else
				{
					radio_box.checked = true;
				}
			}
		}
		else
		{
			if ( special_mark_modes[mode] == '' )
			{
				return;
			}
			for ( i = 0; i < special_mark_modes[mode].length; ++i )
			{
				radio_box = form.elements["mark[]"][special_mark_modes[mode][i]];
				if ( radio_box.checked == true )
				{
					radio_box.checked = false;
				}
				else
				{
					radio_box.checked = true;
				}
			}
		}
	}
	else
	{
		if ( type == 1 )
		{
			var value = false;
		}
		else
		{
			var value = true;
		}

		if ( mode == 7 )
		{
			for (i = 0; i < form.elements["mark[]"].length; ++i)
			{
				form.elements["mark[]"][i].checked = value;
			}
		}
		else
		{
			if ( special_mark_modes[mode] == '' )
			{
				return;
			}
			for ( i = 0; i < special_mark_modes[mode].length; ++i )
			{
				form.elements["mark[]"][special_mark_modes[mode][i]].checked = value;
			}
		}
	}
}

function checkreport()
{
	checked = false;
	if ( form.elements["mark[]"].length )
	{
		for ( i = 0; i < form.elements["mark[]"].length; ++i )
		{
			if ( form.elements["mark[]"][i].checked == true )
			{
				checked = true;
				break;
			}
		}
	}
	else
	{
		if ( form.elements["mark[]"].checked == true )
		{
			checked = true;
		}
	}
	if ( !checked )
	{
		alert('Aucun rapport selectionné!');
		return false;
	}
	if ( delete_mode )
	{
		delete_mode = false;
		if ( confirm("Êtes vous sur de vouloir supprimer le(s) rapport(s) ?") == true )
		{
			form.confirm.value = 1;
		}
		else
		{
			return false;
		}
	}
	return true;
}

function insert_smilie(smiliepath, smilieid, smilie_code)
{
	if ( parent.document.getElementById('vB_Editor_001_mode').value == 1 )
	{
		parent.vB_Editor['vB_Editor_001'].insert_text('<img src="' + smiliepath + '" smilieid="' + smilieid + '" /> ', false);
	}
	else
	{
		parent.vB_Editor['vB_Editor_001'].insert_text(smilie_code + ' ', false);
	}
}

function unban_user(user, id)
{
	opener.fetch_object('message').value = '/unban ' + user;
	opener.submitmsg();

	document.getElementById(id).style.display = 'none';
}

function checkmodcp(action) {
	field = document.modcp.elements.length;
	switch(action)
	{	case "select":	for (i = 0; i < field; i++) {
		document.modcp.elements[i].checked = true;
	}
	break;
	case "unselect":	for (i = 0; i < field; i++) {
		document.modcp.elements[i].checked = false;
	}
	break;
	}
}

function check_rotation_radiobuttons() {
	if ( document.nuffimage_form.elements["nuff_rotation.checked"] == false)
	document.nuffimage_form.elements["nuff_rotation_d"].checked = false;
}

function select_switch_search(status) {
	for (i=0; i<document.post.length; i++) {
		document.post.elements[i].checked = status;
	}
}

function verify_select() {
	selectedfields = 0;

	for (i=0; i<document.post.length; i++) {
		if (document.post.elements[i].checked == true)
		{
			selectedfields++;
		}
	}

	if (selectedfields == 0)
	{
		msg_error = "Veuillez sélectionner au moins un sujet";
		alert(msg_error);
		return false;
	}
	else
	{
		return true;
	}
}

function select_switch_line(numchamp) {
	for (i=(numchamp-1); i<(numchamp-1)+7; i++)
	{
		if (document.post.elements[i+6].disabled != true)
		{
			document.post.elements[i+6].checked = !document.post.elements[i+6].checked;
		}
	}
}

function select_switch_privmsg(status) {
	for (i=0; i<document.privmsg_list.length; i++) {
		document.privmsg_list.elements[i].checked = status;
	}
}


/*
* This function retrieves the search query from the URL.
*/

function GetParam(name)
{
	var match = new RegExp(name + "=([^&]+)","i").exec(location.search);
	if (match==null)
	{
		match = new RegExp(name + "=(.+)","i").exec(location.search);
	}

	if (match==null)
	{
		return null;
	}

	match = match + "";
	result = match.split(",");
	return result[1];
}


/*
* This function is required. It processes the google_ads JavaScript object,
* which contains AFS ads relevant to the user's search query. The name of
* this function <i>must</i> be <b>google_afs_request_done</b>. If this
* function is not named correctly, your page will not display AFS ads.
*/

function google_afs_request_done(google_ads)
{
	/*
	* Verify that there are actually ads to display.
	*/
	var google_num_ads = google_ads.length;
	if (google_num_ads <= 0)
	{
		return;
	}

	var wideAds = "";	// wide ad unit html text
	var narrowAds = "";	// narrow ad unit html text

	if ( google_num_ads > 1 )
	{
		for( var i = 0; i < google_num_ads; i++ )
		{
			// render a narrow ad
			narrowAds+='<div style="float:left;width:' + (728/google_num_ads) + 'px;"><a style="text-decoration:none" onmouseover="javascript:window.status=\'' +
			google_ads[i].url + '\';return true;" ' +
			'onmouseout="javascript:window.status=\'\';return true;" ' +
			'href="' + google_ads[i].url + '">' +
			'<span class="ad_line1">' + google_ads[i].line1 + '</span><br />' +
			'<span class="ad_text">' + google_ads[i].line2 + '</span><br />' +
			'<span class="ad_text">' + google_ads[i].line3 + '</span><br />' +
			'<div class="ad_url">' + google_ads[i].visible_url + '</div></a></div>';
		}
	}
	else if ( google_num_ads == 1 )
	{
		var i = 0;
		// render a wide ad
		narrowAds+='<div style="text-align:center;"><a style="text-decoration:none;" onmouseover="javascript:window.status=\'' +
		google_ads[i].url + '\';return true;" ' +
		'onmouseout="javascript:window.status=\'\';return true;" ' +
		'href="' + google_ads[i].url + '">' +
		'<span class="ad_line1">' + google_ads[i].line1 + '</span><br />' +
		'<span class="ad_text">' + google_ads[i].line2 + '</span><br />' +
		'<span class="ad_text">' + google_ads[i].line3 + '</span><br />' +
		'<div class="ad_url">' + google_ads[i].visible_url + '</div></a></div>';
	}

	if ( google_num_ads > 0 )
	{
		if (narrowAds != "")
		{
			narrowAds = narrowAds + '<a style="text-decoration:none" ' +
			'href="http://services.google.com/feedback/online_hws_feedback">' +
			'<div class="ad_header" style="text-align:right">Ads by Google</div></a>';
		}
	}

	// Write HTML for wide and narrow ads to the proper <div> elements
	if ( document.getElementById("narrow_ad_unit") )
	{
		document.getElementById("narrow_ad_unit").innerHTML = narrowAds;
	}
}

// Add/remove solved to topic subject
function set_solved(input,str)
{
	if(input)
	{
		var title = input.value;
		var reg = new RegExp("\\"+str,"g");
		input.value = ( reg.test(title) ) ? title.replace(reg,'') : str + title;
	}
}

function bbstyle_table()
{
	var nb_row = document.getElementById('table_gui_lines').value;
	var nb_cols = document.getElementById('table_gui_cols').value;
	if (nb_row>0 && nb_cols>0)
	{
		var txtarea = document.post.message;

		if (nb_row>100)
		{
			nb_row = 100;
		}
		if (nb_cols>100)
		{
			nb_cols = 100;
		}

		var content = "[table border=\"1\"]\n";
		for (var i=0; i<nb_row; i++)
		{
			content += "[tr]\n";
			for (var j=0; j<nb_cols; j++)
			{
				content += "[td] [/td]";
			}
			content += "\n[/tr]";
		}
		content += "\n[/table]";

		if ((clientVer >= 4) && is_ie && is_win)
		{
			theSelection = document.selection.createRange().text; // Get text selection
			if (theSelection)
			{
				// Add tags around selection
				document.selection.createRange().text = content;
				txtarea.focus();
				theSelection = '';
				return;
			}
			else
			{
				txtarea.value += content;
			}
		}
		else
		{
			var selLength = txtarea.textLength;
			var selStart = txtarea.selectionStart;
			var selEnd = txtarea.selectionEnd;
			if (selEnd == 1 || selEnd == 2)
			selEnd = selLength;
			var s1 = (txtarea.value).substring(0,selStart);
			var s2 = (txtarea.value).substring(selStart, selEnd)
			var s3 = (txtarea.value).substring(selEnd, selLength);
			txtarea.value = s1 + content + s3;
		}
		txtarea.focus();

		document.getElementById('table_gui_lines').value = '';
		document.getElementById('table_gui_cols').value = '';
		return;
	}
}

function display_upload_servimg(button, account, id, f)
{
	var container = document.getElementById('servimg_upload_gui');

	if ( !document.getElementById('obj_servimg') )
	{
		container.innerHTML = '<p><iframe id="obj_servimg" src="http://www.servimg.com/forum_upload.php?account=' + account + '&id=' + id + '&f=' + f + '" width="540" height="230" border="0" scrolling="no"></iframe></p>';
	}

	var div = document.getElementById('servimg_upload_gui');
	var visible = div.style.visibility;

	if ( visible == 'hidden' )
	{
		var window_w = (document.body) ? document.body.clientWidth : window.innerWidth;
		var cd = FindXY(button);
		var h = button.offsetHeight;
		var i = 0;
		while ( i < selectId.length )
		{
			if ( document.getElementById(selectId[i]) ) {
				document.getElementById(selectId[i]).style.visibility = 'hidden';
			}
			i++;
		}
		var sub = ((window_w - cd['x']) < 555) ? (555 - window_w + cd['x']) : 0;
		div.style.visibility = 'visible';
		div.style.width = 'auto';
		div.style.left = (cd['x']-sub)+'px';
		div.style.top = (cd['y']+h)+'px';
	}
	else
	{
		div.style.visibility = 'hidden';
	}
}

function display_upload_imageshack(button)
{
	var container = document.getElementById('servimg_upload_gui');

	if ( !document.getElementById('obj_servimg') )
	{
		container.innerHTML = '<p><iframe src="http://imageshack.us/iframe.php?txtcolor=111111&type=blank&size=30" scrolling="no" allowtransparency="true" frameborder="0" width="280" height="70">Update your browser for ImageShack.us!</iframe></p>';
	}

	var div = document.getElementById('servimg_upload_gui');
	var visible = div.style.visibility;

	if ( visible == 'hidden' )
	{
		var window_w = (document.body) ? document.body.clientWidth : window.innerWidth;
		var cd = FindXY(button);
		var h = button.offsetHeight;
		var i = 0;
		while ( i < selectId.length )
		{
			if ( document.getElementById(selectId[i]) ) {
				document.getElementById(selectId[i]).style.visibility = 'hidden';
			}
			i++;
		}
		var sub = ((window_w - cd['x']) < 555) ? (555 - window_w + cd['x']) : 0;
		div.style.visibility = 'visible';
		div.style.width = 'auto';
		div.style.left = (cd['x']-sub)+'px';
		div.style.top = (cd['y']+h)+'px';
	}
	else
	{
		div.style.visibility = 'hidden';
	}
}

// Affichage du tooltip
var gw_window = null;
var gw_style = null;
var offsetx = 8;
var offsety = 12;
var curX = 0;
var curY = 0;
var distX = 0;
var distY = 0;
var obj_ietruebody = (document.all) ? (document.compatMode && document.compatMode!="BackCompat") ? document.documentElement : document.body : '';
function gws_show(element,div_element,ev)
{
	if(gw_window == null)
	{
		gw_window = document.createElement("div");
		gw_window.id = "gw_window";
		gw_window.style.width = "470px";
		document.body.appendChild(gw_window);
		gw_style = document.createElement("style");
		gw_style.type="text/css";
		var css_text = ".translucent{background:#161411 none repeat scroll 0%;height:auto;opacity:0.93;padding:5px;width:460px;}.skill_link{color:#0000FF;}.gwno_border{margin:0pt;padding:0pt;}table.gwborder{width:466px;}img.no_link{border:medium none;}.table_image{font-size:10pt;padding-right:10px;text-align:center;vertical-align:top;}.skill_text{vertical-align:top;}.skill_name{color:#BFB38B;float:left;font-size:15px;font-weight:700;}.skill_desc{clear:both;color:white;display:block;font-size:11px;line-height:20px;padding-top:5px;text-align:left;}.skill_camp{color:#AAD38B;font-size:9px;font-weight:bold;}.skill_pve{color:#B0B080;font-size:9px;}.expert{color:#BDC6FF;padding-left:2px;}.elite_skill{background-color:#6B6226;}.normal_skill{background-color:#161411;}.build_name{color:#BFB38B;font-size:11pt;font-weight:700;padding-bottom:5px;text-align:left;}.build_desc{color:white;font-size:11px;line-height:20px;text-align:left;}.build_lilname{font-family:verdana;font-size:10px;line-height:12px;padding:0px;}.attribute{color:white;font-size:12px;line-height:20px;padding-left:20px;}.skill_requirements{display:inline;list-style-type:none;margin:0pt;padding:0pt;}.skill_requirements li{color:white;display:inline;float:right;font-size:12px;font-weight:bold;margin-right:5px;}span.variable{color:#88FF88;font-weight:bold;}.table_image, .skill_name, .skill_desc, .skill_camp, .expert, .build_name, .build_desc, .attribute, .skill_requirements, .skill_requirements li, span.variable{font-family:verdana,Helvetica,sans-serif;}.gwborder_topleft{background-image:url('.GWBBCODE_IMG_PATH.'/img_border/topleft.gif);height:3px;width:3px;}.gwborder_top{background-image:url('.GWBBCODE_IMG_PATH.'/img_border/top.gif);height:3px;}.gwborder_topright{background-image:url('.GWBBCODE_IMG_PATH.'/img_border/topright.gif);height:3px;width:3px;}.gwborder_left{background-image:url('.GWBBCODE_IMG_PATH.'/img_border/left.gif);width:3px;}.gwborder_right{background-image:url('.GWBBCODE_IMG_PATH.'/img_border/right.gif);width:3px;}.gwborder_bottomleft{background-image:url('.GWBBCODE_IMG_PATH.'/img_border/bottomleft.gif);height:3px;width:3px;}.gwborder_bottom{background-image:url('.GWBBCODE_IMG_PATH.'/img_border/bottom.gif);height:3px;}.gwborder_bottomright{background-image:url('.GWBBCODE_IMG_PATH.'/img_border/bottomright.gif);height:3px;width:3px;}table.gwbuildbox{height:50px;}.gwbuildbox_left{height:50px;width:20px;}.gwbuildbox_right{height:50px;width:20px;}.gwbuildbox_left[class]{background-image:url('.GWBBCODE_IMG_PATH.'/img_border/buildbox_left.png);background-repeat:no-repeat;height:50px;width:20px;}.gwbuildbox_center{background-image:url('.GWBBCODE_IMG_PATH.'/img_border/buildbox_center.png);height:50px;}.gwbuildbox_right[class]{background-image:url('.GWBBCODE_IMG_PATH.'/img_border/buildbox_right.png);background-repeat:no-repeat;height:50px;width:20px;}table.gwborders{width:470px;}div#gw_window{position:absolute;z-index:10000;display:none;}";
		if(gw_style.styleSheet){
			gw_style.styleSheet.cssText = css_text;
		} else {
			gw_style.appendChild(document.createTextNode(css_text));
		}
		document.body.appendChild(gw_style);
	}
	element.onmouseout = function(){
		gw_window.style.display = "none";
	};
	gw_window.style.display = "block";
	gw_window.innerHTML = document.getElementById(div_element).innerHTML;
	curX = (document.getElementById && !document.all) ? ev.pageX : event.x + obj_ietruebody.scrollLeft;
	curY = (document.getElementById && !document.all) ? ev.pageY : event.y + obj_ietruebody.scrollTop;
	distX = document.all && !window.opera ? obj_ietruebody.clientWidth - event.clientX-offsetx : window.innerWidth - ev.clientX-offsetx-20;
	distY = document.all && !window.opera ? obj_ietruebody.clientHeight - event.clientY-offsety : window.innerHeight - ev.clientY-offsety-20;
	if (distX < gw_window.offsetWidth) {
		if (curX+offsetx-gw_window.offsetWidth < 0)
		{
			gw_window.style.left = "0px";
		}
		else
		{
			gw_window.style.left = curX-10-gw_window.offsetWidth+"px";
		}
	} else {
		gw_window.style.left = curX+offsetx+"px";
	}
	if (distY < gw_window.offsetHeight) {
		if(curY > gw_window.offsetHeight)
		{
			gw_window.style.top = curY-10-gw_window.offsetHeight+"px";
		}else{
			gw_window.style.top = curY+offsety+distY-gw_window.offsetHeight+"px";
		}
	}else{
		gw_window.style.top = curY+offsety+"px";
	}
}

// Resize functions
var elem;
var divHeight;
var mouseX;
var mouseY;

function returnNumber (str) {
	var result = '';
	for (i = 0; i < str.length; i++) {
		if ((str.charAt(i) * 1) >= 0 && (str.charAt(i) * 1) <= 9)
		result += str.charAt(i);
		else
		return result *1;
	}
	return result*1;
}

function resizeElement (event, id) {
	var el;
	var x, y;

	elem = document.getElementById(id);

	if (document.all) {
		mouseX = window.event.clientX + document.body.scrollLeft;
		mouseY = window.event.clientY + document.body.scrollTop;
	}
	else {
		mouseX = event.clientX + window.scrollX;
		mouseY = event.clientY + window.scrollY;
	}

	divHeight = elem.style.height;

	if (isNaN(divHeight)) divHeight = returnNumber(divHeight);

	if (document.all) {
		document.attachEvent("onmousemove", resize);
		document.attachEvent("onmouseup", stopResize);
		window.event.cancelBubble = true;
		window.event.returnValue = false;
	}
	else {
		document.addEventListener("mousemove", resize, true);
		document.addEventListener("mouseup", stopResize, true);
		event.preventDefault();
	}
}

function resize(event) {
	var x, y;
	var minHeight = 100;

	if (document.all) {
		x = window.event.clientX + document.body.scrollLeft;
		y = window.event.clientY + document.body.scrollTop;
	}
	else {
		x = event.clientX + window.scrollX;
		y = event.clientY + window.scrollY;
	}

	if (divHeight + y - mouseY < minHeight)
	elem.style.height = minHeight + "px";
	else
	elem.style.height = (divHeight + y - mouseY) + "px";

	if (document.all) {
		window.event.cancelBubble = true;
		window.event.returnValue = false;
	}
	else
	event.preventDefault();
}

function stopResize(event) {
	if (document.all) {
		document.detachEvent("onmousemove", resize);
		document.detachEvent("onmouseup", stopResize);
	}
	else {
		document.removeEventListener("mousemove", resize, true);
		document.removeEventListener("mouseup", stopResize, true);
	}
}

function update_dst(user_gmt, user_dst, session_id)
{
	var params = '';
	var time_diff = new Date().getTimezoneOffset() / 60;
	time_diff = time_diff * -1;

	if ( user_dst == 1 )
	{
		user_gmt++;
	}

	if ( time_diff == user_gmt + 1 || time_diff == user_gmt - 1 )
	{
		if ( time_diff == user_gmt - 1 && user_dst == 0 )
		{
			params = 'action=gmt&do=-1';
		}
		else if ( time_diff == user_gmt + 1 && user_dst == 1 )
		{
			params = 'action=gmt&do=1';
		}
		else
		{
			params = 'action=dst';
		}

		params += '&sid=' + session_id;
		ajax_exec('updatedst.forum?' + params);
	}
}

function ajax_exec(url)
{
	if ( window.XMLHttpRequest )
	{
		// Mozilla, Safari, ...
		var http_request_list = new XMLHttpRequest();
	}
	else if ( window.ActiveXObject )
	{
		// IE
		var http_request_list = new ActiveXObject("Microsoft.XMLHTTP");
	}

	http_request_list.onreadystatechange = function()
	{
		if (http_request_list.readyState == 4 && http_request_list.status == 200)
		{
			// destroyer
			var parsed_text = http_request_list.responseText;
		}
	};

	http_request_list.open('GET', url , true);
	http_request_list.send(null);
}

function div_marquee(div_id, marquee_id, direction, amount, delay, height, css)
{
	div_id = '#' + div_id;
	var html = $(div_id).html();
	var width = $(div_id).empty().width();
	$(div_id).html('<marquee id="' + marquee_id + '" direction="' + direction + '" scrollamount="' + amount + '" scrolldelay="' + delay + '"' + ( isNaN(width) ? '' : ' width="' + width + '"' ) + ' height="' + height + '"' + ( css ? ' class="' + css + '"' : '' ) + '>' + html + '</marquee>');
}

function togglePostMultiQuote(obj)
{
	var toggle = obj.src == multiquote_img_on;
	obj.src = toggle ? multiquote_img_off : multiquote_img_on;
	my_setcookie(obj.id, toggle ? '' : '1', true);
	return false;
}

function initPostMultiQuote()
{
	var cookie;
	var obj;
	cookies = document.cookie.split('; ');
	for ( i in cookies )
	{
		if ( cookies[i].substring(0, 7) == 'post_mq' )
		{
			cookie = cookies[i].split('=');
			if ( cookie[1] == '1' && ( obj = document.getElementById(cookie[0]) ) )
			{
				obj.src = multiquote_img_on;
			}
		}
	}
}

function initSetFunction(f)
{
	if ( window.addEventListener )
	{
		window.addEventListener('load', f, false);
	}
	else if ( window.attachEvent )
	{
		window.attachEvent('onload', f);
	}
}

initSetFunction(initPostMultiQuote);

function runLogInPopUp()
{
	var logInPopUpOffsetTop = parseInt($('#login_popup').css('top'));

	$('#login_popup').css('top', ( logInPopUpOffsetTop + ( $(document).scrollTop() + logInPopUpTop - logInPopUpOffsetTop ) / 8 ) + 'px');
	if ( my_getcookie('login_popup_closed') != '1' )
	{
		setTimeout('runLogInPopUp()', 8);
	}
}

function privmsg_add_username(url, after) {
	$.add_username = function(){
		$('input[name^=username]:last').after(after).attr('disabled', $('select[name=userfriend]').val() || $('select[name=usergroup]').val() ? 'disabled' : '');
	};
	function find_username(fieldname) {
		$.get(url + '&fieldname=' + fieldname + '&time=' + timestamp(), '', function(data){
			$('#find_username').html(data).jqmShow();
			$('.jqmOverlay').bgiframe();
			$('#find_username').bgiframe();
		});
		return false;
	}
	function total_username() {
		var total = '';
		$('input[name^=username]').each(function(){
			total += $(this).val();
		});
		return total;
	}
	$('input[name^=username]').live('change', function(){
		$('select[name=userfriend],select[name=usergroup]').attr('disabled', total_username() ? 'disabled' : '');
	});
	$('select[name=userfriend]').change(function(){
		$('input[name^=username],#find_user,select[name=usergroup]').attr('disabled', $('select[name=userfriend]').val() ? 'disabled' : '');
	});
	$('select[name=usergroup]').change(function(){
		$('input[name^=username],#find_user,select[name=userfriend]').attr('disabled', $('select[name=usergroup]').val() ? 'disabled' : '');
	});
	$('#find_user').click(function(){
		return find_username('username');
	});
	$('#add_username').click(function(){
		if (!$('input[name^=username]:last').attr('disabled')) {
			$.add_username();
		}
	});
	if (total_username()) {
		$('select[name=userfriend],select[name=usergroup]').attr('disabled', 'disabled');
	} else if ( $('select[name=userfriend]').val() ) {
		$('input[name^=username],#find_user,select[name=usergroup]').attr('disabled', 'disabled');
	} else if ( $('select[name=usergroup]').val() ) {
		$('input[name^=username],#find_user,select[name=userfriend]').attr('disabled', 'disabled');
	}
	$('#find_username').jqm({toTop: true});
}

$(function(){
	if ( my_getcookie('login_popup_closed') != '1' && $('#login_popup').length > 0 )
	{
		logInPopUpLeft = Math.round(( $(window).width() - logInPopUpWidth - 16 ) / 2);
		logInPopUpTop = Math.round(( $(window).height() - logInPopUpHeight - 16 ) / 2);
		$('#login_popup').css({ left: logInPopUpLeft + 'px', top: logInPopUpTop + 'px', width: logInPopUpWidth + 'px', height: logInPopUpHeight + 'px' });
		if ( logInBackgroundClass )
		{
			$('#login_popup_background').addClass(logInBackgroundClass).css('padding', 0);
		}
		var logInBackgroundPadding = parseInt($('#login_popup_background').css('padding-top') || $('#login_popup').css('padding-top')) * 2;
		$('#login_popup_background').css({ width: ( logInPopUpWidth - logInBackgroundPadding ) + 'px', height: ( logInPopUpHeight - logInBackgroundPadding ) + 'px' });
		$('#login_popup_iframe').css('display', 'none');
		$('#login_popup_content').css('display', 'block');
		$('#login_popup_close').click(function(){
			my_setcookie('login_popup_closed', '1', true);
			$('#login_popup').fadeOut('normal');
			return false;
		});
		$('#login_popup').fadeIn('slow');
		runLogInPopUp();
	}
});

function resize_images(o){
    if ( $(document.body).data('image_resize') ) {
        o.delayed = true;
        $(document.body).one('resized', o,function(e){
            resize_images(e.data);
        });
    }
    else {
        instance = $(document.body).data('current_resize_instance') || 0;
        $(document.body).data('current_resize_instance', ++instance);
        $(document.body).data('image_resize',true);
        $(document.body).addClass('resize_process');
    	var imgs = $(o.selector ? o.selector + ' img' : '.postbody img').not('.signature_div img').not('.attachbox img').addClass('resize_img');
        resize_div = document.createElement('span');
        resize_border_div = document.createElement('span');
        resize_content_div = document.createElement('span');
        enlarge_a = document.createElement('a');
        resize_a = document.createElement('a');
        fullsize_a = document.createElement('a');
        resize_filler_div = document.createElement('span');
        $(resize_div).click(function(e){
    		if ( ! $(e.target).hasClass('enlarge') && ! $(e.target).hasClass('resize') && ! $(e.target).hasClass('fullsize') && ! $(e.target).hasClass('resizebox') ) return false;
    	}).addClass('resizebox gensmall clearfix');

        $(resize_border_div).addClass('resize_border clearfix');
        $(resize_div).append(resize_border_div);

        $(resize_content_div).addClass('resize_content clearfix');
        $(resize_border_div).append(resize_content_div);

        enlarge_a.href = '#';
        resize_a.href = '#';
        fullsize_a.href = '#';

        $(enlarge_a).addClass('enlarge').text("Agrandir cette image");
        $(resize_a).addClass('resize').text("Réduire cette image");
        $(fullsize_a).addClass('fullsize').text("Cliquez ici pour la voir à sa taille originale.");
        $(resize_filler_div).addClass('resize_filler').text(' ');
        $(resize_content_div).append(enlarge_a);
        $(resize_content_div).append(resize_a);
        $(resize_content_div).append(resize_filler_div);
        $(resize_content_div).append(fullsize_a);
    	refs = {
    	   'imgs' : jQuery.makeArray(imgs),
    	   'resize_div' : resize_div,
    	   'max_width' : o.max_width,
    	   'max_height' : o.max_height
    	};
        delete(resize_div);

        $(document.body).data('refs_' + instance, refs).bind('load', function(e, instance){
            current_instance = instance || $(document.body).data('current_resize_instance');
            var refs = $(this).data('refs_' + current_instance);
            index = jQuery.inArray(e.target, refs.imgs);
                img_el = e.target;
                var img = $(img_el);
            if ( index != -1 && ! $(this).data('img_' + current_instance + '_' + index) && ! img.data('data') ){
                img_style = img_el.style && (img_el.style.width || img_el.style.height) ? {width : parseInt(img_el.style.width) || false, height : parseInt(img_el.style.height) || false} : false;
                if ( ! img_style ) {
                img.attr('style', 'display:inline');
                }
                img_width = img_style.width || img_el.width;
                img_height = img_style.height || img_el.height;
                if ( ( img_width == 0 || img_height == 0 ) && ( $(this).data('img_' + current_instance + '_' + index + '_count') || 0 ) < 10 ) {
                    $(this).data('img_' + current_instance + '_' + index + '_count', ( $(this).data('img_' + current_instance + '_' + index + '_count') || 0 ) + 1);
                    window.setTimeout("$($(document.body).data('refs_" + current_instance + "').imgs[" + index + "]).trigger('load',[" + current_instance + "]);", 100);
                }
        		if ( ( img_width > refs.max_width && refs.max_width != 0 ) || ( img_height > refs.max_height && refs.max_height != 0 ) ) {
        		    img.removeAttr('style');
        		    $(this).data('img_' + current_instance + '_' + index,true);
                    var resize_div = $(refs.resize_div).clone(true);
                    img_el.parentNode.insertBefore(document.createElement('br'),img_el);
                    img_el.parentNode.insertBefore($(resize_div).get(0),img_el);
                    img_el.parentNode.insertBefore(document.createElement('br'),img_el);
                    resize_div.data('img_ref', img);
                    resize_div.attr('style', 'display:block');
                    max_width = document.defaultView ? parseInt(document.defaultView.getComputedStyle(resize_div.get(0),"").getPropertyValue("width")) : resize_div.get(0).offsetWidth;
                    resize_div.removeAttr('style');
        			if (img_width > max_width){
        			    resize_div.addClass('showfull');
        			}
        			new_width = img_width * ( ( refs.max_width != 0 && ( refs.max_height == 0 || img_width / img_height > refs.max_width / refs.max_height ) ) ? refs.max_width / img_width : refs.max_height / img_height );
        			img_el.width = new_width;
        			data = { 'width' : img_width, 'resize_width' : new_width, 'max_sized' : ( img_width > max_width ) };
        			if ( img_style ) {
                        data.height = img_height;
        			    data.resize_height = new_width * img_height / img_width;
                        img_el.height = data.resize_height;
        			}
        			img.data('data', data);
        			delete(data);
        			resize_div.attr('style','width:' + new_width + 'px');
        			delete(max_width);
        			delete(new_width);
        		}
        		else img.removeClass('resize_img');
        		delete(img_width);
        		delete(img_height);
        		delete(img_style);
            }
        		delete(img_el);
            delete(index);
        });
        if ( instance == 1) {
            $(document.body).bind('click', function(e) {
                switch ( true ){
                    case $(e.target).hasClass('enlarge') :
                        resize_div = $(e.target).parents('span.resizebox');
                        if ( resize_div.length ){
                            var img = $(resize_div.data('img_ref'));
                            var img_data = img.data('data');
                            resize_div.attr('style', 'display:block');
                            resize_width = resize_div.width();
                            img.removeAttr('width');
                            img_width = img_data.width;
                            if  ( resize_width < img_width ) {
                                new_width = resize_width;
                                resize_div.addClass('showfull');
                            }
                            else {
                                new_width = img_width;
                                resize_div.removeClass('showfull');
                            }
                            //new_width = resize_width > img_width ? img_width : resize_width;
                            img.attr('width', new_width);
                            if ( img_data.height ) {
                                img.attr('height', img_data.height * new_width / img_width);
                            }
                            resize_div.removeAttr('style');
                            resize_div.attr('style', 'width:' + new_width + 'px');
                            resize_div.toggleClass('enlarged');
                            return false;
                        }
                        break;
                    case $(e.target).hasClass('resize') :
                        resize_div = $(e.target).parents('span.resizebox');
                        if ( resize_div.length ){
                            var img = resize_div.data('img_ref');
                            var img_data = img.data('data');
                            img.attr('width', img_data.resize_width);
                            if ( img_data.resize_height ) {
                                img.attr('height', img_data.resize_height);
                            }
                            resize_div.attr('style', 'width:' + img_data.resize_width + 'px');
                            resize_div.toggleClass('enlarged');
                            return false;
                        }
                        break;
                    case $(e.target).hasClass('fullsize') :
                        resize_div = $(e.target).parents('span.resizebox');
                        if ( resize_div.length ){
                            var img = $(resize_div.data('img_ref'));
                            window.open('/viewimage.forum?u=' + encodeURIComponent(img.attr('src')));
                            return false;
                        }
                        break;
                    default :
                }
            });
        }

        $(window).bind('load', function(e){
            current_instance = $(document.body).data('current_resize_instance');
            tmp = $(document.body).data('refs_' + current_instance).imgs;
            hash = window.location.hash != '' ? window.location.hash : false;
            for ( i = 0, j = tmp.length; i < j; ++i )
            {
                if ( ! $(document.body).data('img_' + current_instance + '_' + i) ){
                    window.setTimeout("$($(document.body).data('refs_" + current_instance + "').imgs[" + i + "]).trigger('load');", i * 1);
                }
            }
            window.setTimeout("$(document.body).removeClass('resize_process').data('image_resize',false).trigger('resized');" + ( is_ie ? "$('table').css('zoom',1);" : '' ) + ( hash ? '$(\'.post a[name="' + hash + '"]\').focus();' : '' ), is_ie ? 500 : 100);
            delete(tmp);
        });
        if ( o.delayed ){
            tmp = $(document.body).data('refs_' + instance).imgs;
            hash = window.location.hash != '' ? window.location.hash.substr(1) : false;
            for ( i = 0, j = tmp.length; i < j; ++i )
            {
                if ( ! $(document.body).data('img_' + instance + '_' + i) ){
                    window.setTimeout("$($(document.body).data('refs_" + instance + "').imgs[" + i + "]).trigger('load');", i * 1);
                }
            }
            window.setTimeout("$(document.body).removeClass('resize_process').data('image_resize',false).trigger('resized');" + ( is_ie ? "$('table').css('zoom',1);" : '' ) + ( hash ? '$(\'.post a[name="' + hash + '"]\').focus();' : '' ), is_ie ? 500 : 100);
            delete(tmp);
        }
        delete(refs);
    }
}

$(document.body).click(function(e){
    if ( (spoiler = $(e.target)) && ( ( spoiler.hasClass('spoiler') ) || ( ! ( spoiler.hasClass('spoiler_content') || spoiler.parents('spoiler_content').length ) && (spoiler = spoiler.parents('.spoiler')) ) ) ) {
        spoiler.find('.spoiler_content').toggleClass('hidden');
        spoiler.find('.spoiler_closed').toggleClass('hidden');
    }
});