<div style="width:500px;margin:0 auto; text-align:center;">

<div class="confirmInfo" style="text-align:center"><?php echo _INSTALL_L36?></div>
<br />
<div style="width:250px; float:left;margin-left:10px;text-align:left;">
<?php echo '<img src="'.XOOPS_URL.'/images/xc_legacy.jpg" width="232" height="222" alt="XOOPS Cube Legacy" />';
?>
</div>
<div style="width:230px;float:left;margin-left:10px;text-align:left;">
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
<option value="Kwajalein">Kwajalein(-12.00)</option>
<option value="Pacific/Midway">Pacific/Midway(-11.00)</option>
<option value="Pacific/Honolulu">Pacific/Honolulu(-10.00)</option>
<option value="America/Anchorage">America/Anchorage(-9.00)</option>
<option value="America/Los_Angeles">America/Los_Angeles(-8.00)</option>
<option value="America/Denver">America/Denver(-7.00)</option>
<option value="America/Tegucigalpa">America/Tegucigalpa(-6.00)</option>
<option value="America/New_York">America/New_York(-5.00)</option>
<option value="America/Caracas">America/Caracas(-4.30)</option>
<option value="America/Halifax">America/Halifax(-4.00)</option>
<option value="America/St_Johns">America/St_Johns(-3.30)</option>
<option value="America/Argentina/Buenos_Aires">America/Argentina/Buenos_Aires(-3.00)</option>
<option value="Atlantic/South_Georgia">Atlantic/South_Georgia(-2.00)</option>
<option value="Atlantic/Azores">Atlantic/Azores(-1.00)</option>
<option value="Europe/Dublin" selected="selected">Europe/Dublin(0)</option>
<option value="Europe/Belgrade">Europe/Belgrade(1.00)</option>
<option value="Europe/Minsk">Europe/Minsk(2.00)</option>
<option value="Asia/Kuwait">Asia/Kuwait(3.00)</option>
<option value="Asia/Tehran">Asia/Tehran(3.30)</option>
<option value="Asia/Muscat">Asia/Muscat(4.00)</option>
<option value="Asia/Yekaterinburg">Asia/Yekaterinburg(5.00)</option>
<option value="Asia/Kolkata">Asia/Kolkata(5.30)</option>
<option value="Asia/Katmandu">Asia/Katmandu(5.45)</option>
<option value="Asia/Dhaka">Asia/Dhaka(6.00)</option>
<option value="Asia/Rangoon">Asia/Rangoon(6.30)</option>
<option value="Asia/Krasnoyarsk">Asia/Krasnoyarsk(7.00)</option>
<option value="Asia/Brunei">Asia/Brunei(8.00)</option>
<option value="Asia/Seoul">Asia/Seoul(9.00)</option>
<option value="Australia/Darwin">Australia/Darwin(9.30)</option>
<option value="Australia/Canberra">Australia/Canberra(10.00)</option>
<option value="Asia/Magadan">Asia/Magadan(11.00)</option>
<option value="Pacific/Fiji">Pacific/Fiji(12.00)</option>
<option value="Pacific/Tongatapu">Pacific/Tongatapu(13.00)</option>
</select>';
}?>
</div>

</div>
