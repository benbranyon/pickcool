$=function(t,e,n,i,o,r,s,u,c,f,l,h){return h=function(t,e){return new h.i(t,e)},h.i=function(i,o){n.push.apply(this,i?i.nodeType||i==t?[i]:""+i===i?/</.test(i)?((u=e.createElement(o||"q")).innerHTML=i,u.children):(o&&h(o)[0]||e).querySelectorAll(i):/f/.test(typeof i)?/c/.test(e.readyState)?i():h(e).on("DOMContentLoaded",i):i:n)},h.i[l="prototype"]=(h.extend=function(t){for(f=arguments,u=1;u<f.length;u++)if(l=f[u])for(c in l)t[c]=l[c];return t})(h.fn=h[l]=n,{on:function(t,e){return t=t.split(i),this.map(function(n){(i[u=t[0]+(n.b$=n.b$||++o)]=i[u]||[]).push([e,t[1]]),n["add"+r](t[0],e)}),this},off:function(t,e){return t=t.split(i),l="remove"+r,this.map(function(n){if(f=i[t[0]+n.b$],u=f&&f.length)for(;c=f[--u];)e&&e!=c[0]||t[1]&&t[1]!=c[1]||(n[l](t[0],c[0]),f.splice(u,1));else!t[1]&&n[l](t[0],e)}),this},is:function(t){return u=this[0],(u.matches||u["webkit"+s]||u["moz"+s]||u["ms"+s]).call(u,t)}}),h}(window,document,[],/\.(.+)/,0,"EventListener","MatchesSelector");
$.fn.hasClass = function( className ) {
    return !!this[ 0 ] && this[ 0 ].classList.contains( className );
};
$.fn.addClass = function( className ) {
    this.forEach( function( item ) {
        var classList = item.classList;
        classList.add.apply( classList, className.split( /\s/ ) );
    });
    return this;
};
$.fn.removeClass = function( className ) {
    this.forEach( function( item ) {
        var classList = item.classList;
        classList.remove.apply( classList, className.split( /\s/ ) );
    });
    return this;
};
$.fn.toggleClass = function( className, b ) {
    this.forEach( function( item ) {
        var classList = item.classList;
        if( typeof b !== 'boolean' ) {
            b = !classList.contains( className );
        }
        classList[ b ? 'add' : 'remove' ].apply( classList, className.split( /\s/ ) );
    });
    return this;
};