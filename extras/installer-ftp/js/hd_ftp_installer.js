var hdFtpInstaller = Class.create({
  
  allparams: '',
  
  install_lang: null,
    
  initialize: function()
  {
      if ($('ftpcheck_b')){
          var b1 = $('ftpcheck_b')
          Event.observe(b1, 'click', this.checkFtp.bindAsEventListener(this, b1));
//          Event.observe(b1, 'click', this.copyFile.bindAsEventListener(this, b1));
      }
  },
  
  checkFtp: function(e, button)
  {
      Element.show($('loading'));
      button.disabled = true;
      Element.addClassName(button, 'loading');
      
      var thisparams = '&ftp_username='+$('ftp_username').value+'&ftp_password='+$('ftp_password').value;
      this.allparams += thisparams;
      
      var ajax = new Ajax.Request(
          './index.php',
          {
            parameters: 'action_json_ftpcheck=1&lang='+lang+thisparams,
            onComplete: this.onComplete.bind(this, button, 'getRepository',
                                             [$('ftp_username'),$('ftp_password')])
          }
          );
  },
  
  getRepository: function(e, button)
  {
      Element.show($('loading'));
      button.disabled = true;
      Element.addClassName(button, 'loading');
      
      var thisparams = '&repository_url='+$('repository_url').value;
      this.allparams += thisparams;
      
      var ajax = new Ajax.Request(
          './index.php',
          {
            parameters: 'action_json_getrepository=1&lang='+lang+thisparams,
            onComplete: this.onComplete.bind(this, button, 'selectPackage',
                                             [$('repository_url')])
          }
          );
  },
  
  
  selectPackage: function(e, button)
  {
      Element.show($('loading'));
      button.disabled = true;
      Element.addClassName(button, 'loading');
      
      var packageRadios = $A($('package_list').getElementsByTagName('INPUT'));
      var target_package = '';
      for (var i=0,j=packageRadios.length; i<j; i++){
          if (packageRadios[i].checked){
              target_package = packageRadios[i].value;
          }
      }
      
      var thisparams = '&target_package='+target_package;
      this.allparams += thisparams;
      
      var ajax = new Ajax.Request(
          './index.php',
          {
            parameters: 'action_json_xoopsparam=1&lang='+lang+thisparams,
            onComplete: this.onComplete.bind(this, button, 'xoopsParam',
                                             packageRadios)
          });
  },
  
  xoopsParam: function(e, button)
  {
      // installFiles
      button.disabled = true;
      Element.show($('installprocess'));
      $('xoops_root_path').disabled = true;
      $('xoops_trust_path').disabled = true;
      
      this.allparams += '&xoops_root_path='+$('xoops_root_path').value+'&xoops_trust_path='+$('xoops_trust_path').value;
      
      var ajax = new Ajax.Request(
          './index.php',
          {
            parameters: 'action_json_downloadpackage=1&lang='+lang+this.allparams,
            onComplete: this.onCompleteInstallProcess.bind(this, 'extractFile', button)
          });
  },
  
  
  //// install phase
  extractFile: function(button)
  {
      $('installprocessError').innerHTML = '';
      var ajax = new Ajax.Request(
          './index.php',
          {
            parameters: 'action_json_extractfile=1&lang='+lang+'&'+this.allparams,
            onComplete: this.onCompleteInstallProcess.bind(this, 'copyFile', button)
                                                           
                                                           
          });
  },
  
  copyFile: function(button)
  {
      $('installprocessError').innerHTML = '';
      var ajax = new Ajax.Request(
          './index.php',
          {
            parameters: 'action_json_copyfile=1&lang='+lang+'&'+this.allparams,
            onComplete: this.onCompleteInstallProcess.bind(this, 'checkXOOPSInstallerURL', button)
          }
          );
  },
  
  checkXOOPSInstallerURL: function(button, http)
  {
//      $('installprocessError').innerHTML = '';
//      alert(Event.element(button));
//      Element.removeClassName(button, 'loading');
//      $('main').innerHTML += http.responseText;
      $('processIndicator').lastChild.className = 'success';
      
      var xoops_url = $('xoops_url').href;
      var ajax = new Ajax.Request(
          xoops_url+'/install/',
          {
            onComplete: this.inputXOOPSInstallParams.bind(this)
          }
          );
  },
  
  inputXOOPSInstallParams: function(http, json)
  {
      if (http.status == "200"){
          Element.show("xoopsURLcheckOK");
          // set language select tab
          $('temp').innerHTML = http.responseText;
          var lang_sel = $('temp').getElementsByTagName('SELECT')[0];
          this.install_lang = lang_sel.value ;
          Event.observe(lang_sel, 'change', this.changeInstallLanguage.bindAsEventListener(this));
          $('xoops_language_selector').appendChild(lang_sel);
          Event.observe($('installXOOPS_b'), 'click', this.processXOOPSCubeInstallWizard.bind(this));
      }
      else {
          Element.show("xoopsURLcheckFailed");
      }
  },
  
  
  processXOOPSCubeInstallWizard: function(e)
  {
      // cleanup wizard log
      Element.show($('loading'));
      Element.scrollTo($('footer'));
      var logs = $('wizardIndicator').childNodes;
      for (var i=0,j=logs.length; i<j; i++){ if (i>0 && logs[i]){ Element.remove(logs[i]); } }
      
      var button = Event.element(e);
      button.disabled = true;
      Element.show('wizardProcess');
      
      var params = 'lang='+this.install_lang+'&';
      params += this.getXoopsWizardParams();
      
      var ops = ['dbsave_hd',
                 'serversettings_save',
                 'checkDB',
                 'createDB',
                 'createTables',
                 'insertData_hd'];
                
      // check function
      var isOk = function(header){
          var inputs = header.getElementsByTagName('INPUT');
          for (var i=0,j=inputs.length; i<j; i++){
              if (inputs[i].type == 'submit'){ return true;}
          }
          return false;
      }
      
      var xoops_url = $('xoops_url').href;
      var result = true;
      for (var i=0,j=ops.length; i<j; i++){
          if (!ops[i]){ break ;}
          var result_log = null;
          var ajax = new Ajax.Request(
              xoops_url+'/install/',
              {
                asynchronous: false, /* STEP by STEP */
                parameters: params + 'op=' + ops[i],
                onComplete: (function(){
                    return function(http, json)
                    {
                        $('temp').innerHTML = http.responseText;
                        // get log
                        var tables = $('temp').getElementsByTagName('TABLE');
                        if (tables.length > 0){
                            result_log = tables;
                            /// DB create or not
                            if (ops[i] == 'checkDB'){
                                var createDB = false;
                                $A(tables[0].getElementsByTagName('IMG')).each(function(img){
                                    if (img.src.match(/img\/no\.gif$/)){ createDB =true; }
                                });
                                if (!createDB){
                                    ops = ops.without('createDB');
                                }
                            }
                            /// to 2nd xoops installer
                            if (ops[i] == 'insertData_hd'){
                                // success
                                this.processXoops2ndInstaller();
                            }
                        }
                        else {
                            var paras = $('temp').getElementsByTagName('P');
                            if (paras.length > 0){
                                result_log = paras;
                            }
                        }
                        if (result_log){
                            var li = document.createElement('LI');
                            for (var ii=0,jj=result_log.length; ii<jj; ii++){
                                if (result_log[ii]) li.appendChild(result_log[ii]);
                            }
                            $('wizardIndicator').appendChild(li);
                        }
                    };
                })().bind(this)
              });
          if (!isOk($('header'))){
              // install failed
              button.disabled = false;
              this.cleanUp1stInstalledTables();
              break;
          }
      }
      Element.hide($('loading'));
  },
  
  
  /// 2nd Installer process
  processXoops2ndInstaller: function()
  {
      Element.show($('loading'));
      Element.show($('install2ndProcess'));
      Element.scrollTo($('footer'));
      
      /// Let's login!
      var xoops_url = $('xoops_url').href;
      var ajax = new Ajax.Request(
          xoops_url+'/user.php',
          {
            parameters: 'uname='+$('uname').value+'&pass='+$('upass').value+'&xoops_login=1',
            onComplete: (function(){
                return function(http, json){
                    var li = document.createElement('LI');
                    $('temp').innerHTML = http.responseText;
                    var a = $('temp').getElementsByTagName('A');
                    if (a.length > 0){
                        if (!a[0].href.match(/user\.php\?/)){
                            // success
                            Element.show('loginSuccess');
                            this.processModulesInstall();
                        }
                        else {
                            Element.show('loginFailed');
                            this.installFinish();
                        }
                    }
                    else {
                        // already logined
                        Element.show('loginSuccess');
                        this.doInstallModules(http,json);
                    }
                };
            })().bind(this)
          });
  },
  
  
  // installCompletedConfirm
  processModulesInstall: function()
  {
      var xoops_url = $('xoops_url').href;
      var ajax = new Ajax.Request(
          xoops_url+'/index.php',
          {
            method: 'get',
            onComplete: this.doInstallModules.bind(this)

          });
  },
  
  // do modules install
  doInstallModules: function(http, json)
  {
      $('temp').innerHTML =  http.responseText;
      var xoops_url = $('xoops_url').href;
      var ajax = new Ajax.Request(
          xoops_url+'/index.php',
          {
            parameters: Form.serialize($('temp').getElementsByTagName('FORM')[0]),
            onComplete: this.installFinish.bind(this)
          });
      
  },
  
  /// cleanup xoops2 tables which 1st installer created.
  cleanUp1stInstalledTables: function()
  {
      Element.scrollTo($('footer'));
      Element.show($('loading'));
      
      var params = '&';
      params += this.getXoopsWizardParams();
      
      var ajax = new Ajax.Request(
          './index.php',
          {
            parameters: 'action_json_cleanup1sttables=1&lang='+lang+params,
            onComplete: function(http, json){
                var div = document.createElement('DIV');
                div.innerHTML = http.responseText;
                $('wizardIndicator').appendChild(div);
            }
          });
      
  },
  
  
  onComplete: function(button, next_action,  forms, http, json)
  {
      Element.removeClassName(button, 'loading');
      Element.hide($('loading'));
      Element.show($(next_action));
      button.disabled = false;
      $(next_action).innerHTML = http.responseText;
      if (json.result && json.result==1){
          // success
          for (var i=0,j=forms.length; i<j; i++){
              forms[i].disabled = true;
          }
          button.disabled = true;
          var next_button = $(next_action+'_b');
          Event.observe(next_button, 'click', this[next_action].bindAsEventListener(this, next_button));
      }
      else {
          // failed
          if (json.error){
              // alert(json.error);
          }
          for (var i=0,j=forms.length; i<j; i++){
              forms[i].disabled = false;
          }
      }
  },
  
  onCompleteInstallProcess: function(next_action, button, http, json)
  {
      var ul = $('processIndicator');
      var li = ul.lastChild;
      
      
      if (json.result && json.result == 1){
          $('installprocessSuccess').innerHTML += http.responseText;
          li.className = 'success';
          var nextli = document.createElement('LI');
          nextli.className = 'process';
          nextli.innerHTML = json.next_message ;
          ul.appendChild(nextli);
          this[next_action](button, http);
      } else {
          li.className = 'failed';
          $('installprocessError').innerHTML = http.responseText;
          button.disabled = false;
          $('xoops_root_path').disabled = false;
          $('xoops_trust_path').disabled = false;
      }
  },
  
  changeInstallLanguage: function(e)
  {
      var sel = Event.element(e);
      this.install_lang = sel.value;
  },
  
  getXoopsWizardParams: function()
  {
      var params = '';
      $A($('xoopsWizardParams').getElementsByTagName('INPUT')).each(function(input){
          params += input.name + '=' + input.value + '&';
      });
      
      return params;
  },
  
  /// all finishied
  installFinish: function(http, json)
  {
      $('temp').innerHTML = http.responseText;
      $('AllFinished').firstChild.href = $('xoops_url').href;
      Element.show('moduleInstallSuccess');
      Element.show('AllFinished');
      
      /// remove install directory and this hd_ftp_installer directory
      var params = 'action_json_removedir=1&ftp_username='+$('ftp_username').value+'&ftp_password='+$('ftp_password').value;
      params += '&'+this.getXoopsWizardParams();
      var ajax = new Ajax.Request(
          './index.php',
          {
            parameters: params
          }
          );
  }

});

Event.observe(window, 'load', function(){
    hfi = new hdFtpInstaller();
});
