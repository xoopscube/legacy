<div style="width:500px;margin:0 auto; text-align:center;">

<div class="confirmInfo" style="text-align:center"><?php echo _INSTALL_L36?></div>
<br />
<div style="width:250px; float:right;margin-left:10px;text-align:left;">
<?php echo '<img src="'.XOOPS_URL.'/images/xc_legacy.jpg" width="232" height="222" alt="XOOPS Cube Legacy" />';
?>
</div>
<div style="width:230px;float:left;margin:0 0 24px 10px;text-align:left;">
<?php echo _INSTALL_L37?><br />
<input type="text" class="adminame" name="adminname" />
<br /><br />
<?php echo _INSTALL_L38?><br />
<input type="text" class="adminmail" name="adminmail" maxlength="60" />
<br /><br />
<?php echo _INSTALL_L39?><br />
<input type="password" class="adminpass" name="adminpass" />
<br /><br />
<?php echo _INSTALL_L74?><br />
<input type="password" class="adminpass2" name="adminpass2" />
<br /><br />
<?php if(version_compare(phpversion(), '5.3.0', '>=')){
echo _INSTALL_L77 .'<br />
<select name="timezone">
<option value="Kwajalein">'._TZ_GMTM12.'</option>
<option value="Pacific/Midway">'._TZ_GMTM11.'</option>
<option value="Pacific/Honolulu">'._TZ_GMTM10.'</option>
<option value="America/Adak">'._TZ_GMTM9.'</option>
<option value="America/Anchorage">'._TZ_GMTM8.'</option>
<option value="America/Los_Angeles">'._TZ_GMTM7.'</option>
<option value="America/Denver">'._TZ_GMTM6.'</option>
<option value="America/Guayaquil">'._TZ_GMTM5.'</option>
<option value="America/Caracas">'._TZ_GMTM45.'</option>
<option value="America/New_York">'._TZ_GMTM4.'</option>
<option value="America/Halifax">'._TZ_GMTM3.'</option>
<option value="Atlantic/South_Georgia">'._TZ_GMTM2.'</option>
<option value="Atlantic/Cape_Verde">'._TZ_GMTM1.'</option>
<option value="Atlantic/Azores" selected="selected">'._TZ_GMT0.'</option>
<option value="Europe/Dublin">'._TZ_GMTP1.'</option>
<option value="Europe/Belgrade">'._TZ_GMTP2.'</option>
<option value="Asia/Kuwait">'._TZ_GMTP3.'</option>
<option value="Asia/Tehran">'._TZ_GMTP35.'</option>
<option value="Asia/Muscat">'._TZ_GMTP4.'</option>
<option value="Asia/Kabul">'._TZ_GMTP45.'</option>
<option value="Asia/Ashgabat">'._TZ_GMTP5.'</option>
<option value="Asia/Kolkata">'._TZ_GMTP55.'</option>
<option value="Asia/Kathmandu">'._TZ_GMTP575.'</option>
<option value="Asia/Dhaka">'._TZ_GMTP6.'</option>
<option value="Asia/Rangoon">'._TZ_GMTP65.'</option>
<option value="Asia/Jakarta">'._TZ_GMTP7.'</option>
<option value="Asia/Krasnoyarsk">'._TZ_GMTP8.'</option>
<option value="Asia/Seoul">'._TZ_GMTP9.'</option>
<option value="Australia/Darwin">'._TZ_GMTP95.'</option>
<option value="Asia/Yakutsk">'._TZ_GMTP10.'</option>
<option value="Australia/Canberra">'._TZ_GMTP11.'</option>
<option value="Pacific/Fiji">'._TZ_GMTP12.'</option>
<option value="Pacific/Tongatapu">'._TZ_GMTP13.'</option>
</select>';
}?>
</div>

</div>
