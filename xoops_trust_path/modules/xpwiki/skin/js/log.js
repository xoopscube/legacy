var Log = {
    enable: false,
    
    debug: function(msg){
        Log._log(msg, "debug","black");
    },
    
    info: function(msg){
        Log._log(msg, "info","blue");
    },
    
    warn: function(msg){
        Log._log(msg, "warn","yellow");
    },
    
    error: function(msg){
        Log._log(msg, "error","red");
    },
    
    fatal: function(msg){
        Log._log(msg, "fatal","red");
    },
    
    _log: function(msg,level,color){
        if(Log.enable == false) return;
        var init = new Log._init();
        var ele = init.console;
        if(ele){
        	msg = msg.replace(/</g,"&lt;");
        	msg = msg.replace(/>/g,"&gt;");
           ele.innerHTML = "&gt;&nbsp;<strong style='color:" + color + "'>" + 
                level + "</strong>: " + msg + "<br/>" + 
                ele.innerHTML;
        }
    },
    
    _init: function() {
        var self = arguments.callee;
        if(self.instance == null){
            this.initialize.apply(this,arguments);
            self.instance = this;
        }
        return self.instance;
    }
}

Log._init.prototype = {
    initialize : function(){
        this.consoleElement = document.createElement("div");
        with(this.consoleElement.style){
            position = "fixed";
            top = "5px";
            right = "5px";
            width = "260px";
            backgroundColor = "#cccccc";
            border = "1px solid #333333";
            margin = "0px";
            padding = "0px";
            fontSize = "10pt";
            opacity = "0.9";
        }
        
        //title
        var title = document.createElement("h4");
        with(title.style){
            borderBottom = "1px solid #333333";
            backgroundColor = "#666666";
            padding = "1px 2px";
            margin = "0px";
            fontSize = "9pt";
        }
        title.appendChild(document.createTextNode("Log Console"));
        
        //console
        var console = document.createElement("div");
        with(console.style){
            width = "256px";
            height = "200px";
            overflow = "auto";
            margin = "0px";
            padding = "2px";        
        }
        
        //clear console
        var clearconsole = document.createElement("span");
        clearconsole.appendChild(document.createTextNode("[Clear Console]"));
        clearconsole.onmouseup=function(){console.innerHTML=""};
        clearconsole.style.cursor = "pointer";
        
        //footer
        var footer = document.createElement("div");
        with(footer.style){
            borderTop = "1px solid #333333";
            backgroundColor = "#666666";
            padding = "1px 1px";
            margin = "0px";
            textAlign = "right";
            fontSize = "9pt";
        }
        footer.appendChild(clearconsole);
        
        this.consoleElement.appendChild(title);
        this.consoleElement.appendChild(console);
        this.consoleElement.appendChild(footer);
        document.body.appendChild(this.consoleElement);
        
        this.console = console;
    }
}





