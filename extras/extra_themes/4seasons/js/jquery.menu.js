jQuery(document).ready(function(){
    jQuery("#i_menu").click(function(){
          if (jQuery("#iPhoneMenu").is(":hidden")){
          jQuery("#iPhoneMenu").slideDown("slow");
}
          else{
          jQuery("#iPhoneMenu").slideUp("fast");
               }
        });
});
jQuery(document).ready(function(){
    jQuery("#goback").click(function(){
          jQuery("#iPhoneMenu").slideUp("fast");
        });
});
