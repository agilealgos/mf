(window.__googlesitekit_webpackJsonp=window.__googlesitekit_webpackJsonp||[]).push([[7],{145:function(t,n,e){"use strict";var r=e(2),o=Object(r.createContext)(!1);n.a=o},31:function(t,n,e){"use strict";e.d(n,"b",(function(){return r})),e.d(n,"a",(function(){return o}));var r="core/ui",o="activeContextID"},331:function(t,n,e){"use strict";e.d(n,"a",(function(){return o}));var r=e(386);function o(t){return Object(r.a)(t)}},620:function(t,n,e){"use strict";(function(t){var r,o=e(633),c=e(114),i=e(494),u=e(1097),a=e(404),f=e(1100),s=e(1102),l=e(621),p=e(895),d=e(72),v=e(331),y=Object(o.a)({},null===(r=t.wp)||void 0===r?void 0:r.data);y.combineStores=d.a,y.commonActions=d.b,y.commonControls=d.c,y.commonStore=d.d,y.createReducer=v.a,y.useInViewSelect=p.a,y.createRegistryControl=c.a,y.createRegistrySelector=c.b,y.useSelect=i.a,y.useDispatch=u.a,y.useRegistry=a.a,y.withSelect=f.a,y.withDispatch=s.a,y.RegistryProvider=l.b,n.a=y}).call(this,e(22))},72:function(t,n,e){"use strict";e.d(n,"a",(function(){return P})),e.d(n,"b",(function(){return T})),e.d(n,"c",(function(){return _})),e.d(n,"d",(function(){return D})),e.d(n,"e",(function(){return E})),e.d(n,"g",(function(){return G})),e.d(n,"f",(function(){return x}));var r,o=e(5),c=e.n(o),i=e(24),u=e.n(i),a=e(6),f=e.n(a),s=e(7),l=e.n(s),p=e(206),d=e.n(p),v=e(75),y=e.n(v),b=e(114);function g(t,n){var e=Object.keys(t);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(t);n&&(r=r.filter((function(n){return Object.getOwnPropertyDescriptor(t,n).enumerable}))),e.push.apply(e,r)}return e}function O(t){for(var n=1;n<arguments.length;n++){var e=null!=arguments[n]?arguments[n]:{};n%2?g(Object(e),!0).forEach((function(n){f()(t,n,e[n])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(e)):g(Object(e)).forEach((function(n){Object.defineProperty(t,n,Object.getOwnPropertyDescriptor(e,n))}))}return t}var h=function(){for(var t=arguments.length,n=new Array(t),e=0;e<t;e++)n[e]=arguments[e];var r=n.reduce((function(t,n){return O(O({},t),n)}),{}),o=n.reduce((function(t,n){return[].concat(u()(t),u()(Object.keys(n)))}),[]),c=C(o);return l()(0===c.length,"collect() cannot accept collections with duplicate keys. Your call to collect() contains the following duplicated functions: ".concat(c.join(", "),". Check your data stores for duplicates.")),r},j=h,w=h,m=function(){for(var t=arguments.length,n=new Array(t),e=0;e<t;e++)n[e]=arguments[e];var r,o=[].concat(n);return"function"!=typeof o[0]&&(r=o.shift()),function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:r,n=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{};return o.reduce((function(t,e){return e(t,n)}),t)}},S=h,k=h,R=h,A=function(t){return t},P=function(){for(var t=arguments.length,n=new Array(t),e=0;e<t;e++)n[e]=arguments[e];var r=R.apply(void 0,u()(n.map((function(t){return t.initialState||{}}))));return{initialState:r,controls:w.apply(void 0,u()(n.map((function(t){return t.controls||{}})))),actions:j.apply(void 0,u()(n.map((function(t){return t.actions||{}})))),reducer:m.apply(void 0,[r].concat(u()(n.map((function(t){return t.reducer||A}))))),resolvers:S.apply(void 0,u()(n.map((function(t){return t.resolvers||{}})))),selectors:k.apply(void 0,u()(n.map((function(t){return t.selectors||{}}))))}},T={getRegistry:function(){return{payload:{},type:"GET_REGISTRY"}},await:c.a.mark((function t(n){return c.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.abrupt("return",{payload:{value:n},type:"AWAIT"});case 1:case"end":return t.stop()}}),t)}))},_=(r={},f()(r,"GET_REGISTRY",Object(b.a)((function(t){return function(){return t}}))),f()(r,"AWAIT",(function(t){return t.payload.value})),r),C=function(t){for(var n=[],e={},r=0;r<t.length;r++){var o=t[r];e[o]=e[o]>=1?e[o]+1:1,e[o]>1&&n.push(o)}return n},D={actions:T,controls:_,reducer:A},E=function(t){return function(n){return I(t(n))}},I=y()((function(t){return d()(t,(function(t,n){return function(){var e=t.apply(void 0,arguments);return l()(void 0!==e,"".concat(n,"(...) is not resolved")),e}}))}));function G(t){var n=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},e=n.negate,r=void 0!==e&&e,o=Object(b.b)((function(n){return function(e){var o=!r,c=!!r;try{for(var i=arguments.length,u=new Array(i>1?i-1:0),a=1;a<i;a++)u[a-1]=arguments[a];return t.apply(void 0,[n,e].concat(u)),o}catch(t){return c}}})),c=Object(b.b)((function(n){return function(e){for(var r=arguments.length,o=new Array(r>1?r-1:0),c=1;c<r;c++)o[c-1]=arguments[c];t.apply(void 0,[n,e].concat(o))}}));return{safeSelector:o,dangerousSelector:c}}function x(t,n){return l()("function"==typeof t,"a validator function is required."),l()("function"==typeof n,"an action creator function is required."),l()("Generator"!==t[Symbol.toStringTag]&&"GeneratorFunction"!==t[Symbol.toStringTag],"an action’s validator function must not be a generator."),function(){return t.apply(void 0,arguments),n.apply(void 0,arguments)}}},895:function(t,n,e){"use strict";e.d(n,"a",(function(){return d}));var r=e(24),o=e.n(r),c=e(494),i=e(2),u=e(14),a=e.n(u),f=e(497),s=e(145),l=e(31),p=function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},n=t.sticky,e=void 0!==n&&n,r=Object(i.useContext)(s.a),o=Object(i.useState)(!1),u=a()(o,2),p=u[0],d=u[1],v=Object(c.a)((function(t){return t(l.b).getInViewResetHook()}));return Object(i.useEffect)((function(){r.value&&!p&&d(!0)}),[p,r,d]),Object(f.a)((function(){d(!1)}),[v]),!(!e||!p)||!!r.value},d=function(t){var n=arguments.length>1&&void 0!==arguments[1]?arguments[1]:[],e=p({sticky:!0}),r=Object(i.useRef)(),u=Object(i.useCallback)(t,[].concat(o()(n),[t])),a=Object(c.a)(e?u:function(){});return e&&(r.current=a),r.current}},934:function(t,n,e){"use strict";e.r(n),function(t){var r=e(620);void 0===t.googlesitekit&&(t.googlesitekit={}),t.googlesitekit.data=r.a,n.default=r.a}.call(this,e(22))}},[[934,1,0]]]);