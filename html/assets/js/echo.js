      !function(t,e){"function"==typeof define&&define.amd?define(e):"object"==typeof exports?module.exports=e:t.echo=e(t)}(this,function(t){"use strict";var e,o,n,r,c={},i=function(){},a=function(t,e){var o=t.getBoundingClientRect();return o.right>=e.l&&o.bottom>=e.t&&o.left<=e.r&&o.top<=e.b},d=function(){clearTimeout(o),o=setTimeout(c.render,n)};return c.init=function(o){o=o||{};var a=o.offset||0,l=o.offsetVertical||a,u=o.offsetHorizontal||a,f=function(t,e){return parseInt(t||e,10)};e={t:f(o.offsetTop,l),b:f(o.offsetBottom,l),l:f(o.offsetLeft,u),r:f(o.offsetRight,u)},n=f(o.throttle,250),r=!!o.unload,i=o.callback||i,c.render(),document.addEventListener?(t.addEventListener("scroll",d,!1),t.addEventListener("load",d,!1)):(t.attachEvent("onscroll",d),t.attachEvent("onload",d))},c.render=function(){for(var o,n,d=document.querySelectorAll("img[data-echo]"),l=d.length,u={l:0-e.l,t:0-e.t,b:(t.innerHeight||document.documentElement.clientHeight)+e.b,r:(t.innerWidth||document.documentElement.clientWidth)+e.r},f=0;l>f;f++)n=d[f],a(n,u)?(r&&n.setAttribute("data-echo-placeholder",n.src),n.src=n.getAttribute("data-echo"),r||n.removeAttribute("data-echo"),i(n,"load")):r&&(o=n.getAttribute("data-echo-placeholder"))&&(n.src=o,n.removeAttribute("data-echo-placeholder"),i(n,"unload"));l||c.detach()},c.detach=function(){document.removeEventListener?t.removeEventListener("scroll",d):t.detachEvent("onscroll",d),clearTimeout(o)},c});
      echo.init({
        offset: 1000,
        throttle: 250,
        unload: false,
        debounce: false,
        unload: true,
        callback: function (element, op) {
          console.log(element, 'has been', op + 'ed')
        }
      });
