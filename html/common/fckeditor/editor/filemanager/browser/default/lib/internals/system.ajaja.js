JSmarty.System.read=function(o,R){var k,x,C=this.buildPath(o,R);for(k=C.length-1;0<=k;k--){try{x=System.readFile(C[k]);this.modified[o]=new Date().getTime();break;}catch(Z){}}return x||function(){JSmarty.Error.log("System","can't load the "+o);return null;}();};JSmarty.System.outputString=function(){print(Array.prototype.join.call(arguments,""));};