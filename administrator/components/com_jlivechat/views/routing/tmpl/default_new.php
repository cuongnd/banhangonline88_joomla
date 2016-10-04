<?php 
/**
 * @package JLive! Chat
 * @version 4.3.3
 * @copyright (C) Copyright 2008-2010 CMS Fruit, CMSFruit.com. All rights reserved.
 * @license GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.txt

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU Lesser General Public License as published by
 the Free Software Foundation; either version 3 of the License, or (at your
 option) any later version.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public
 License for more details.

 You should have received a copy of the GNU Lesser General Public License
 along with this program.  If not, see http://www.gnu.org/licenses/.
 */

defined('_JEXEC') or die('Restricted access');
?>
<script language="Javascript">
    byCityTxt = '<?php echo addslashes(JText::_('CITY')); ?>';
    byCountryTxt = '<?php echo addslashes(JText::_('COUNTRY')); ?>';
    byIPTxt = '<?php echo addslashes(JText::_('IP_ADDRESS')); ?>';
    byTimeTxt = '<?php echo addslashes(JText::_('TIME')); ?>';
    byDayTxt = '<?php echo addslashes(JText::_('DAY_OF_WEEK')); ?>';
    byGroupTxt = '<?php echo addslashes(JText::_('USER_GROUP')); ?>';
    byStatusTxt = '<?php echo addslashes(JText::_('USER_STATUS')); ?>';
    
    selectAllTrafficLbl = '<?php echo addslashes(JText::_('SELECT_ALL_TRAFFIC')); ?>';

    prepYUI();
</script>
<form action="" method="post" name="adminForm" id="adminForm" onsubmit="return selectAllOperators();">
    <?php echo JText::_('ROUTE_NAME_LBL'); ?> <input type="text" name="route_name" value="<?php echo JRequest::getVar('route_name', '', 'method'); ?>" size="35" maxlength="150" />
    <br />
    <br />
    <h3><?php echo JText::_('SOURCE_CRITERIA') ?></h3>
    <label style="margin-left: 30px;"></label>
    <label id="change_filter_type-container">
	<select id="change_filter_type" name="change_filter_type" onchange="changeFilterType();">
	    <option value=""><?php echo JText::_('FILTER_BY_LBL'); ?></option>
	    <option value="by_city"><?php echo JText::_('CITY'); ?></option>
	    <option value="by_country"><?php echo JText::_('COUNTRY'); ?></option>
	    <option value="by_ip_address"><?php echo JText::_('IP_ADDRESS'); ?></option>
	    <option value="by_time"><?php echo JText::_('TIME'); ?></option>
	    <option value="by_day"><?php echo JText::_('DAY_OF_WEEK'); ?></option>
	    <option value="by_group"><?php echo JText::_('USER_GROUP'); ?></option>
	    <option value="by_user_status"><?php echo JText::_('USER_STATUS'); ?></option>
	</select>
    </label>
    <div class="by_city filter-layer hide">
	<?php echo JText::_('CITY_NAME'); ?>: <input type="text" value="" maxlength="150" />
	<button id="add_city_filter_btn"><?php echo JText::_('ADD_FILTER'); ?></button>
    </div>
    <div class="by_country filter-layer hide">
	<label id="by_country_filter-container">
	<?php echo JText::_('COUNTRY'); ?>: <select id="by_country_filter" name="by_country_filter" onchange="changeCountryFilter(this.value);">
	    <option value=""><?php echo JText::_('SELECT_COUNTRY_OPTION'); ?></option>
	    <option value="AF">Afghanistan</option>
	    <option value="AX">ÅLand Islands</option>
	    <option value="AL">Albania</option>
	    <option value="DZ">Algeria</option>
	    <option value="AS">American Samoa</option>
	    <option value="AD">Andorra</option>
	    <option value="AO">Angola</option>
	    <option value="AI">Anguilla</option>
	    <option value="AQ">Antarctica</option>
	    <option value="AG">Antigua And Barbuda</option>
	    <option value="AR">Argentina</option>
	    <option value="AM">Armenia</option>
	    <option value="AW">Aruba</option>
	    <option value="AU">Australia</option>
	    <option value="AT">Austria</option>
	    <option value="AZ">Azerbaijan</option>
	    <option value="BS">Bahamas</option>
	    <option value="BH">Bahrain</option>
	    <option value="BD">Bangladesh</option>
	    <option value="BB">Barbados</option>
	    <option value="BY">Belarus</option>
	    <option value="BE">Belgium</option>
	    <option value="BZ">Belize</option>
	    <option value="BJ">Benin</option>
	    <option value="BM">Bermuda</option>
	    <option value="BT">Bhutan</option>
	    <option value="BO">Bolivia</option>
	    <option value="BA">Bosnia And Herzegovina</option>
	    <option value="BW">Botswana</option>
	    <option value="BV">Bouvet Island</option>
	    <option value="BR">Brazil</option>
	    <option value="IO">British Indian Ocean Territory</option>
	    <option value="BN">Brunei Darussalam</option>
	    <option value="BG">Bulgaria</option>
	    <option value="BF">Burkina Faso</option>
	    <option value="BI">Burundi</option>
	    <option value="KH">Cambodia</option>
	    <option value="CM">Cameroon</option>
	    <option value="CA">Canada</option>
	    <option value="CV">Cape Verde</option>
	    <option value="KY">Cayman Islands</option>
	    <option value="CF">Central African Republic</option>
	    <option value="TD">Chad</option>
	    <option value="CL">Chile</option>
	    <option value="CN">China</option>
	    <option value="CX">Christmas Island</option>
	    <option value="CC">Cocos (Keeling) Islands</option>
	    <option value="CO">Colombia</option>
	    <option value="KM">Comoros</option>
	    <option value="CG">Congo</option>
	    <option value="CD">Congo, The Democratic Republic Of The</option>
	    <option value="CK">Cook Islands</option>
	    <option value="CR">Costa Rica</option>
	    <option value="CI">Cote D'Ivoire</option>
	    <option value="HR">Croatia</option>
	    <option value="CU">Cuba</option>
	    <option value="CY">Cyprus</option>
	    <option value="CZ">Czech Republic</option>
	    <option value="DK">Denmark</option>
	    <option value="DJ">Djibouti</option>
	    <option value="DM">Dominica</option>
	    <option value="DO">Dominican Republic</option>
	    <option value="EC">Ecuador</option>
	    <option value="EG">Egypt</option>
	    <option value="SV">El Salvador</option>
	    <option value="GQ">Equatorial Guinea</option>
	    <option value="ER">Eritrea</option>
	    <option value="EE">Estonia</option>
	    <option value="ET">Ethiopia</option>
	    <option value="FK">Falkland Islands (Malvinas)</option>
	    <option value="FO">Faroe Islands</option>
	    <option value="FJ">Fiji</option>
	    <option value="FI">Finland</option>
	    <option value="FR">France</option>
	    <option value="GF">French Guiana</option>
	    <option value="PF">French Polynesia</option>
	    <option value="TF">French Southern Territories</option>
	    <option value="GA">Gabon</option>
	    <option value="GM">Gambia</option>
	    <option value="GE">Georgia</option>
	    <option value="DE">Germany</option>
	    <option value="GH">Ghana</option>
	    <option value="GI">Gibraltar</option>
	    <option value="GR">Greece</option>
	    <option value="GL">Greenland</option>
	    <option value="GD">Grenada</option>
	    <option value="GP">Guadeloupe</option>
	    <option value="GU">Guam</option>
	    <option value="GT">Guatemala</option>
	    <option value="Gg">Guernsey</option>
	    <option value="GN">Guinea</option>
	    <option value="GW">Guinea-Bissau</option>
	    <option value="GY">Guyana</option>
	    <option value="HT">Haiti</option>
	    <option value="HM">Heard Island And Mcdonald Islands</option>
	    <option value="VA">Holy See (Vatican City State)</option>
	    <option value="HN">Honduras</option>
	    <option value="HK">Hong Kong</option>
	    <option value="HU">Hungary</option>
	    <option value="IS">Iceland</option>
	    <option value="IN">India</option>
	    <option value="ID">Indonesia</option>
	    <option value="IR">Iran, Islamic Republic Of</option>
	    <option value="IQ">Iraq</option>
	    <option value="IE">Ireland</option>
	    <option value="IM">Isle Of Man</option>
	    <option value="IL">Israel</option>
	    <option value="IT">Italy</option>
	    <option value="JM">Jamaica</option>
	    <option value="JP">Japan</option>
	    <option value="JE">Jersey</option>
	    <option value="JO">Jordan</option>
	    <option value="KZ">Kazakhstan</option>
	    <option value="KE">Kenya</option>
	    <option value="KI">Kiribati</option>
	    <option value="KP">Korea, Democratic People'S Republic Of</option>
	    <option value="KR">Korea, Republic Of</option>
	    <option value="KW">Kuwait</option>
	    <option value="KG">Kyrgyzstan</option>
	    <option value="LA">Lao People'S Democratic Republic</option>
	    <option value="LV">Latvia</option>
	    <option value="LB">Lebanon</option>
	    <option value="LS">Lesotho</option>
	    <option value="LR">Liberia</option>
	    <option value="LY">Libyan Arab Jamahiriya</option>
	    <option value="LI">Liechtenstein</option>
	    <option value="LT">Lithuania</option>
	    <option value="LU">Luxembourg</option>
	    <option value="MO">Macao</option>
	    <option value="MK">Macedonia, The Former Yugoslav Republic Of</option>
	    <option value="MG">Madagascar</option>
	    <option value="MW">Malawi</option>
	    <option value="MY">Malaysia</option>
	    <option value="MV">Maldives</option>
	    <option value="ML">Mali</option>
	    <option value="MT">Malta</option>
	    <option value="MH">Marshall Islands</option>
	    <option value="MQ">Martinique</option>
	    <option value="MR">Mauritania</option>
	    <option value="MU">Mauritius</option>
	    <option value="YT">Mayotte</option>
	    <option value="MX">Mexico</option>
	    <option value="FM">Micronesia, Federated States Of</option>
	    <option value="MD">Moldova, Republic Of</option>
	    <option value="MC">Monaco</option>
	    <option value="MN">Mongolia</option>
	    <option value="MS">Montserrat</option>
	    <option value="MA">Morocco</option>
	    <option value="MZ">Mozambique</option>
	    <option value="MM">Myanmar</option>
	    <option value="NA">Namibia</option>
	    <option value="NR">Nauru</option>
	    <option value="NP">Nepal</option>
	    <option value="NL">Netherlands</option>
	    <option value="AN">Netherlands Antilles</option>
	    <option value="NC">New Caledonia</option>
	    <option value="NZ">New Zealand</option>
	    <option value="NI">Nicaragua</option>
	    <option value="NE">Niger</option>
	    <option value="NG">Nigeria</option>
	    <option value="NU">Niue</option>
	    <option value="NF">Norfolk Island</option>
	    <option value="MP">Northern Mariana Islands</option>
	    <option value="NO">Norway</option>
	    <option value="OM">Oman</option>
	    <option value="PK">Pakistan</option>
	    <option value="PW">Palau</option>
	    <option value="PS">Palestinian Territory, Occupied</option>
	    <option value="PA">Panama</option>
	    <option value="PG">Papua New Guinea</option>
	    <option value="PY">Paraguay</option>
	    <option value="PE">Peru</option>
	    <option value="PH">Philippines</option>
	    <option value="PN">Pitcairn</option>
	    <option value="PL">Poland</option>
	    <option value="PT">Portugal</option>
	    <option value="PR">Puerto Rico</option>
	    <option value="QA">Qatar</option>
	    <option value="RE">Reunion</option>
	    <option value="RO">Romania</option>
	    <option value="RU">Russian Federation</option>
	    <option value="RW">Rwanda</option>
	    <option value="SH">Saint Helena</option>
	    <option value="KN">Saint Kitts And Nevis</option>
	    <option value="LC">Saint Lucia</option>
	    <option value="PM">Saint Pierre And Miquelon</option>
	    <option value="VC">Saint Vincent And The Grenadines</option>
	    <option value="WS">Samoa</option>
	    <option value="SM">San Marino</option>
	    <option value="ST">Sao Tome And Principe</option>
	    <option value="SA">Saudi Arabia</option>
	    <option value="SN">Senegal</option>
	    <option value="CS">Serbia And Montenegro</option>
	    <option value="SC">Seychelles</option>
	    <option value="SL">Sierra Leone</option>
	    <option value="SG">Singapore</option>
	    <option value="SK">Slovakia</option>
	    <option value="SI">Slovenia</option>
	    <option value="SB">Solomon Islands</option>
	    <option value="SO">Somalia</option>
	    <option value="ZA">South Africa</option>
	    <option value="GS">South Georgia And The South Sandwich Islands</option>
	    <option value="ES">Spain</option>
	    <option value="LK">Sri Lanka</option>
	    <option value="SD">Sudan</option>
	    <option value="SR">Suriname</option>
	    <option value="SJ">Svalbard And Jan Mayen</option>
	    <option value="SZ">Swaziland</option>
	    <option value="SE">Sweden</option>
	    <option value="CH">Switzerland</option>
	    <option value="SY">Syrian Arab Republic</option>
	    <option value="TW">Taiwan, Province Of China</option>
	    <option value="TJ">Tajikistan</option>
	    <option value="TZ">Tanzania, United Republic Of</option>
	    <option value="TH">Thailand</option>
	    <option value="TL">Timor-Leste</option>
	    <option value="TG">Togo</option>
	    <option value="TK">Tokelau</option>
	    <option value="TO">Tonga</option>
	    <option value="TT">Trinidad And Tobago</option>
	    <option value="TN">Tunisia</option>
	    <option value="TR">Turkey</option>
	    <option value="TM">Turkmenistan</option>
	    <option value="TC">Turks And Caicos Islands</option>
	    <option value="TV">Tuvalu</option>
	    <option value="UG">Uganda</option>
	    <option value="UA">Ukraine</option>
	    <option value="AE">United Arab Emirates</option>
	    <option value="GB">United Kingdom</option>
	    <option value="US">United States</option>
	    <option value="UM">United States Minor Outlying Islands</option>
	    <option value="UY">Uruguay</option>
	    <option value="UZ">Uzbekistan</option>
	    <option value="VU">Vanuatu</option>
	    <option value="VE">Venezuela</option>
	    <option value="VN">Viet Nam</option>
	    <option value="VG">Virgin Islands, British</option>
	    <option value="VI">Virgin Islands, U.S.</option>
	    <option value="WF">Wallis And Futuna</option>
	    <option value="EH">Western Sahara</option>
	    <option value="YE">Yemen</option>
	    <option value="ZM">Zambia</option>
	    <option value="ZW">Zimbabwe</option>
	</select>
	</label>
	<button id="add_country_filter_btn"><?php echo JText::_('ADD_FILTER'); ?></button>
    </div>
    <div class="by_ip_address filter-layer hide">
	<?php echo JText::_('IP_ADDRESS'); ?>: <input type="text" value="" maxlength="15" />
	<button id="ip_address_filter_btn"><?php echo JText::_('ADD_FILTER'); ?></button>
	(<?php echo JText::_('example'); ?> 192.168.1.10 or 192.168.1.)
    </div>
    <div class="by_time filter-layer hide">
	<?php echo JText::_('BETWEEN_HOURS'); ?>,
	<?php echo JText::_('FROM_LBL'); ?>
	<select name="from_hour">
	    <?php for($a = 0; $a <= 23; $a++) { ?>
	    <option value="<?php echo sprintf('%02d', $a); ?>"><?php echo sprintf('%02d', $a); ?></option>
	    <?php } ?>
	</select>
	<select name="from_minute">
	    <?php for($a = 0; $a <= 59; $a++) { ?>
	    <option value="<?php echo sprintf('%02d', $a); ?>"><?php echo sprintf('%02d', $a); ?></option>
	    <?php } ?>
	</select>
	 - <?php echo JText::_('To:'); ?>
	<select name="to_hour">
	    <?php for($a = 0; $a <= 23; $a++) { ?>
	    <option value="<?php echo sprintf('%02d', $a); ?>"><?php echo sprintf('%02d', $a); ?></option>
	    <?php } ?>
	</select>
	<select name="to_minute">
	    <?php for($a = 0; $a <= 59; $a++) { ?>
	    <option value="<?php echo sprintf('%02d', $a); ?>"><?php echo sprintf('%02d', $a); ?></option>
	    <?php } ?>
	</select>
	<button id="time_filter_btn"><?php echo JText::_('ADD_FILTER'); ?></button>
	(* Universal Time - UTC)
    </div>
    <div class="by_day filter-layer hide">
	<?php echo JText::_('DAYS'); ?>:
	<input type="checkbox" value="Sunday" /><?php echo JText::_('SUNDAY'); ?>
	<input type="checkbox" value="Monday" /><?php echo JText::_('MONDAY'); ?>
	<input type="checkbox" value="Tuesday" /><?php echo JText::_('TUESDAY'); ?>
	<input type="checkbox" value="Wednesday" /><?php echo JText::_('WEDNESDAY'); ?>
	<input type="checkbox" value="Thursday" /><?php echo JText::_('THURSDAY'); ?>
	<input type="checkbox" value="Friday" /><?php echo JText::_('FRIDAY'); ?>
	<input type="checkbox" value="Saturday" /><?php echo JText::_('SATURDAY'); ?>
	
	<button id="days_of_week_filter_btn"><?php echo JText::_('ADD_FILTER'); ?></button>
    </div>
    <div class="by_group filter-layer hide">
	<?php echo JText::_('GROUP'); ?>:  <select>
	    <option value="">- <?php echo JText::_('SELECT_GROUP'); ?> -</option>
	    <?php for($a = 0; $a < count($this->groups); $a++) { ?>
	    <option value="<?php echo $this->groups[$a]['id']; ?>"><?php echo $this->groups[$a]['name']; ?></option>
	    <?php } ?>
	</select>
	<button id="group_filter_btn"><?php echo JText::_('ADD_FILTER'); ?></button>
    </div>
    <div class="by_user_status filter-layer hide">
	<?php echo JText::_('USER_STATUS'); ?>: <select id="user_status_filter" name="user_status_filter">
	    <option value="Guest"><?php echo JText::_('GUEST'); ?></option>
	    <option value="Registered"><?php echo JText::_('REGISTERED'); ?></option>
	</select>
	<button id="user_status_filter_btn"><?php echo JText::_('ADD_FILTER'); ?></button>
    </div>
    <br />
    <table id="filters-table" class="form" width="400">
	<thead>
	    <tr>
		<td>
		    <?php echo JText::_('TYPE'); ?>
		</td>
		<td>
		    <?php echo JText::_('EQUALS'); ?>
		</td>
		<td>&nbsp;</td>
	    </tr>
	</thead>
	<tbody>
	    <tr class="place-holder">
		<td>
		    <?php echo JText::_('SELECT_ALL_TRAFFIC'); ?>
		</td>
		<td colspan="2">
		    *:*
		    <input type="hidden" name="source[]" value="*:*" />
		</td>
	    </tr>
	</tbody>
    </table>
    <br />
    <h3><?php echo JText::_('DESTINATION') ?></h3>
    <table class="form">
	<tr>
	    <td class="label"><?php echo JText::_('ACTION'); ?>:</td>
	    <td>
		<select style="margin-left: 30px;" id="route_action" name="route_action" onchange="changeAction();">
		    <option value="send_to_all_operators"><?php echo JText::_('SEND_TO_ALL_OPERATORS'); ?></option>
		    <option value="send_to_selected_operators"><?php echo JText::_('SEND_TO_SELECTED_OPERATORS'); ?></option>
		    <option value="send_to_offline"><?php echo JText::_('SEND_TO_OFFLINE_MESSAGE'); ?></option>
		</select><select style="margin-left: 20px;" name="ring_type">
		    <option value="default">- <?php echo JText::_('DEFAULT_RING_ORDER'); ?> -</option>
		    <option value="ring_at_same_time"><?php echo JText::_('RING_OPERATORS_AT_SAME_TIME'); ?></option>
		    <option value="ring_in_order"><?php echo JText::_('RING_OPERATORS_IN_ORDER'); ?></option>
		</select>
	    </td>
	</tr>
    </table>
    <br />
    <table class="form">
	<tr>
	    <td>
		<?php echo JText::_('AVAILABLE_OPERATORS'); ?>
	    </td>
	    <td>
		&nbsp;
	    </td>
	    <td>
		<?php echo JText::_('SELECTED_OPERATORS'); ?>
	    </td>
	</tr>
	<tr>
	    <td valign="top">
		<select id="available_operators" multiple="multiple" size="10" style="width: 230px; height: 130px;" disabled="disabled">
		    <?php for($a = 0; $a < count($this->operators); $a++) { ?>
		    <option value="<?php echo $this->operators[$a]['operator_id']; ?>"><?php echo $this->operatoradmin->operatorKeyLabel($this->operators[$a]['operator_id']); ?></option>
		    <?php } ?>
		</select>
	    </td>
	    <td>
		<a href="javascript: void(0);" onclick="addSelectedOperators();"><img src="components/com_jlivechat/assets/images/arrows/right_arrow.gif" width="27" height="27" alt="<?php echo JText::_('ADD_SELECTED_OPERATOR'); ?>" border="0" /></a>
		<br /><br />
		<a href="javascript: void(0);" onclick="removeSelectedOperators();"><img src="components/com_jlivechat/assets/images/arrows/left_arrow.gif" width="27" height="27" alt="<?php echo JText::_('REMOVE_SELECTED_OPERATOR'); ?>" border="0" /></a>
	    </td>
	    <td valign="top">
		<select id="selected_operators" name="selected_operators[]" multiple="multiple" size="10" style="width: 230px; height: 130px;" disabled="disabled">
		    
		</select>
	    </td>
	</tr>
	<tr>
	    <td valign="top">&nbsp;</td>
	    <td>&nbsp;</td>
	    <td valign="top" align="right">
		<a href="javascript: void(0);" onclick="clearSelectedOperators();"><?php echo JText::_('Clear'); ?></a>
	    </td>
	</tr>
    </table>
    
    <script type="text/javascript">
	var filterByMenu = new YAHOO.widget.Button({
	    id: "change_filter_type-menubutton",
	    name: "change_filter_type-menubutton",
	    label: "<em class=\"yui-button-label\"><?php echo JText::_('FILTER_BY_LBL'); ?></em>",
	    type: "menu",
	    menu: "change_filter_type",
	    container: "change_filter_type-container"
	});

	var oAddCountryFilterBtn = new YAHOO.widget.Button("add_country_filter_btn");
	var oAddCityFilterBtn = new YAHOO.widget.Button("add_city_filter_btn");
	var oAddIPAddressFilterBtn = new YAHOO.widget.Button("ip_address_filter_btn");
	var oAddUserStatusFilterBtn = new YAHOO.widget.Button("user_status_filter_btn");
	var oAddGroupFilterBtn = new YAHOO.widget.Button("group_filter_btn");
	var oAddDaysOfWeekFilterBtn = new YAHOO.widget.Button("days_of_week_filter_btn");
	var oAddTimeFilterBtn = new YAHOO.widget.Button("time_filter_btn");

	oAddCountryFilterBtn.on("click", addFilter);
	oAddCityFilterBtn.on("click", addFilter);
	oAddIPAddressFilterBtn.on("click", addFilter);
	oAddUserStatusFilterBtn.on("click", addFilter);
	oAddGroupFilterBtn.on("click", addFilter);
	oAddDaysOfWeekFilterBtn.on("click", addFilter);
	oAddTimeFilterBtn.on("click", addFilter);

	filterByMenu.on("selectedMenuItemChange", onFilterByMenuItemChange);
    </script>

    <br />
    <input type="hidden" name="task" value="create_route" />
    
    <?php echo JHTML::_( 'form.token' ); ?>
</form>

