{* file to handle db changes in 4.7.11 during upgrade *}

-- CRM-19134 Missing French overseas departments.
INSERT INTO civicrm_state_province (id, country_id, abbreviation, name) VALUES
  (NULL, 1076, "GP", "Guadeloupe"),
  (NULL, 1076, "MQ", "Martinique"),
  (NULL, 1076, "GF", "Guyane"),
  (NULL, 1076, "RE", "La Réunion"),
  (NULL, 1076, "YT", "Mayotte");

-- CRM-17663 Fix missing dashboard names
UPDATE civicrm_dashboard SET name = 'activity' WHERE (name IS NULL OR name = '') AND url LIKE "civicrm/dashlet/activity?%";
UPDATE civicrm_dashboard SET name = 'myCases' WHERE (name IS NULL OR name = '') AND url LIKE "civicrm/dashlet/myCases?%";
UPDATE civicrm_dashboard SET name = 'allCases' WHERE (name IS NULL OR name = '') AND url LIKE "civicrm/dashlet/allCases?%";
UPDATE civicrm_dashboard SET name = 'casedashboard' WHERE (name IS NULL OR name = '') AND url LIKE "civicrm/dashlet/casedashboard?%";

-- CRM-19291 Fix names on dashlets where name is an empty string
{if $multilingual}
UPDATE civicrm_dashboard SET name = label_{$locales.0} WHERE name = '';
{else}
UPDATE civicrm_dashboard SET name = label WHERE name = '';
{/if}

-- CRM-18508 Display State/Province in event address in registration emails
{include file='../CRM/Upgrade/4.7.11.msg_template/civicrm_msg_template.tpl'}

-- CRM-19034 Fix Capitlisation of Country names.
UPDATE civicrm_country SET name = "American Samoa" WHERE lower(name) = lower("American Samoa");
UPDATE civicrm_country SET name = "Andorra" WHERE lower(name) = lower("Andorra");
UPDATE civicrm_country SET name = "Angola" WHERE lower(name) = lower("Angola");
UPDATE civicrm_country SET name = "Anguilla" WHERE lower(name) = lower("Anguilla");
UPDATE civicrm_country SET name = "Antarctica" WHERE lower(name) = lower("Antarctica");
UPDATE civicrm_country SET name = "Antigua and Barbuda" WHERE lower(name) = lower("Antigua and Barbuda");
UPDATE civicrm_country SET name = "Argentina" WHERE lower(name) = lower("Argentina");
UPDATE civicrm_country SET name = "Armenia" WHERE lower(name) = lower("Armenia");
UPDATE civicrm_country SET name = "Aruba" WHERE lower(name) = lower("Aruba");
UPDATE civicrm_country SET name = "Australia" WHERE lower(name) = lower("Australia");
UPDATE civicrm_country SET name = "Austria" WHERE lower(name) = lower("Austria");
UPDATE civicrm_country SET name = "Azerbaijan" WHERE lower(name) = lower("Azerbaijan");
UPDATE civicrm_country SET name = "Bahrain" WHERE lower(name) = lower("Bahrain");
UPDATE civicrm_country SET name = "Bangladesh" WHERE lower(name) = lower("Bangladesh");
UPDATE civicrm_country SET name = "Barbados" WHERE lower(name) = lower("Barbados");
UPDATE civicrm_country SET name = "Belarus" WHERE lower(name) = lower("Belarus");
UPDATE civicrm_country SET name = "Belgium" WHERE lower(name) = lower("Belgium");
UPDATE civicrm_country SET name = "Belize" WHERE lower(name) = lower("Belize");
UPDATE civicrm_country SET name = "Benin" WHERE lower(name) = lower("Benin");
UPDATE civicrm_country SET name = "Bermuda" WHERE lower(name) = lower("Bermuda");
UPDATE civicrm_country SET name = "Bhutan" WHERE lower(name) = lower("Bhutan");
UPDATE civicrm_country SET name = "Bolivia" WHERE lower(name) = lower("Bolivia");
UPDATE civicrm_country SET name = "Bosnia and Herzegovina" WHERE lower(name) = lower("Bosnia and Herzegovina");
UPDATE civicrm_country SET name = "Botswana" WHERE lower(name) = lower("Botswana");
UPDATE civicrm_country SET name = "Bouvet Island" WHERE lower(name) = lower("Bouvet Island");
UPDATE civicrm_country SET name = "Brazil" WHERE lower(name) = lower("Brazil");
UPDATE civicrm_country SET name = "British Indian Ocean Territory" WHERE lower(name) = lower("British Indian Ocean Territory");
UPDATE civicrm_country SET name = "Virgin Islands, U.S." WHERE lower(name) = lower("Virgin Islands, U.S.");
UPDATE civicrm_country SET name = "Brunei Darussalam" WHERE lower(name) = lower("Brunei Darussalam");
UPDATE civicrm_country SET name = "Bulgaria" WHERE lower(name) = lower("Bulgaria");
UPDATE civicrm_country SET name = "Burkina Faso" WHERE lower(name) = lower("Burkina Faso");
UPDATE civicrm_country SET name = "Myanmar" WHERE lower(name) = lower("Myanmar");
UPDATE civicrm_country SET name = "Burundi" WHERE lower(name) = lower("Burundi");
UPDATE civicrm_country SET name = "Cambodia" WHERE lower(name) = lower("Cambodia");
UPDATE civicrm_country SET name = "Cameroon" WHERE lower(name) = lower("Cameroon");
UPDATE civicrm_country SET name = "Canada" WHERE lower(name) = lower("Canada");
UPDATE civicrm_country SET name = "Cape Verde" WHERE lower(name) = lower("Cape Verde");
UPDATE civicrm_country SET name = "Cayman Islands" WHERE lower(name) = lower("Cayman Islands");
UPDATE civicrm_country SET name = "Central African Republic" WHERE lower(name) = lower("Central African Republic");
UPDATE civicrm_country SET name = "Chad" WHERE lower(name) = lower("Chad");
UPDATE civicrm_country SET name = "Chile" WHERE lower(name) = lower("Chile");
UPDATE civicrm_country SET name = "China" WHERE lower(name) = lower("China");
UPDATE civicrm_country SET name = "Christmas Island" WHERE lower(name) = lower("Christmas Island");
UPDATE civicrm_country SET name = "Cocos (Keeling) Islands" WHERE lower(name) = lower("Cocos (Keeling) Islands");
UPDATE civicrm_country SET name = "Colombia" WHERE lower(name) = lower("Colombia");
UPDATE civicrm_country SET name = "Comoros" WHERE lower(name) = lower("Comoros");
UPDATE civicrm_country SET name = "Congo, Republic Of The" WHERE lower(name) = lower("Congo, Republic of The");
UPDATE civicrm_country SET name = "Congo, The Democratic Republic of the" WHERE lower(name) = lower("Congo, The Democratic Republic of the");
UPDATE civicrm_country SET name = "Cook Islands" WHERE lower(name) = lower("Cook Islands");
UPDATE civicrm_country SET name = "Costa Rica" WHERE lower(name) = lower("Costa Rica");
UPDATE civicrm_country SET name = "Côte d'Ivoire" WHERE lower(name) = lower("Côte d'Ivoire");
UPDATE civicrm_country SET name = "Croatia" WHERE lower(name) = lower("Croatia");
UPDATE civicrm_country SET name = "Cuba" WHERE lower(name) = lower("Cuba");
UPDATE civicrm_country SET name = "Cyprus" WHERE lower(name) = lower("Cyprus");
UPDATE civicrm_country SET name = "Czech Republic" WHERE lower(name) = lower("Czech Republic");
UPDATE civicrm_country SET name = "Denmark" WHERE lower(name) = lower("Denmark");
UPDATE civicrm_country SET name = "Djibouti" WHERE lower(name) = lower("Djibouti");
UPDATE civicrm_country SET name = "Dominica" WHERE lower(name) = lower("Dominica");
UPDATE civicrm_country SET name = "Dominican Republic" WHERE lower(name) = lower("Dominican Republic");
UPDATE civicrm_country SET name = "Timor-Leste" WHERE lower(name) = lower("Timor-Leste");
UPDATE civicrm_country SET name = "Ecuador" WHERE lower(name) = lower("Ecuador");
UPDATE civicrm_country SET name = "Egypt" WHERE lower(name) = lower("Egypt");
UPDATE civicrm_country SET name = "El Salvador" WHERE lower(name) = lower("El Salvador");
UPDATE civicrm_country SET name = "Equatorial Guinea" WHERE lower(name) = lower("Equatorial Guinea");
UPDATE civicrm_country SET name = "Eritrea" WHERE lower(name) = lower("Eritrea");
UPDATE civicrm_country SET name = "Estonia" WHERE lower(name) = lower("Estonia");
UPDATE civicrm_country SET name = "Ethiopia" WHERE lower(name) = lower("Ethiopia");
UPDATE civicrm_country SET name = "European Union" WHERE lower(name) = lower("European Union");
UPDATE civicrm_country SET name = "Falkland Islands (Malvinas)" WHERE lower(name) = lower("Falkland Islands (Malvinas)");
UPDATE civicrm_country SET name = "Faroe Islands" WHERE lower(name) = lower("Faroe Islands");
UPDATE civicrm_country SET name = "Fiji" WHERE lower(name) = lower("Fiji");
UPDATE civicrm_country SET name = "Finland" WHERE lower(name) = lower("Finland");
UPDATE civicrm_country SET name = "France" WHERE lower(name) = lower("France");
UPDATE civicrm_country SET name = "French Guiana" WHERE lower(name) = lower("French Guiana");
UPDATE civicrm_country SET name = "French Polynesia" WHERE lower(name) = lower("French Polynesia");
UPDATE civicrm_country SET name = "French Southern Territories" WHERE lower(name) = lower("French Southern Territories");
UPDATE civicrm_country SET name = "Gabon" WHERE lower(name) = lower("Gabon");
UPDATE civicrm_country SET name = "Georgia" WHERE lower(name) = lower("Georgia");
UPDATE civicrm_country SET name = "Germany" WHERE lower(name) = lower("Germany");
UPDATE civicrm_country SET name = "Ghana" WHERE lower(name) = lower("Ghana");
UPDATE civicrm_country SET name = "Gibraltar" WHERE lower(name) = lower("Gibraltar");
UPDATE civicrm_country SET name = "Greece" WHERE lower(name) = lower("Greece");
UPDATE civicrm_country SET name = "Greenland" WHERE lower(name) = lower("Greenland");
UPDATE civicrm_country SET name = "Grenada" WHERE lower(name) = lower("Grenada");
UPDATE civicrm_country SET name = "Guadeloupe" WHERE lower(name) = lower("Guadeloupe");
UPDATE civicrm_country SET name = "Guam" WHERE lower(name) = lower("Guam");
UPDATE civicrm_country SET name = "Guatemala" WHERE lower(name) = lower("Guatemala");
UPDATE civicrm_country SET name = "Guinea" WHERE lower(name) = lower("Guinea");
UPDATE civicrm_country SET name = "Guinea-Bissau" WHERE lower(name) = lower("Guinea-Bissau");
UPDATE civicrm_country SET name = "Guyana" WHERE lower(name) = lower("Guyana");
UPDATE civicrm_country SET name = "Haiti" WHERE lower(name) = lower("Haiti");
UPDATE civicrm_country SET name = "Heard Island and McDonald Islands" WHERE lower(name) = lower("Heard Island and McDonald Islands");
UPDATE civicrm_country SET name = "Holy See (Vatican City State)" WHERE lower(name) = lower("Holy See (Vatican City State)");
UPDATE civicrm_country SET name = "Honduras" WHERE lower(name) = lower("Honduras");
UPDATE civicrm_country SET name = "Hong Kong" WHERE lower(name) = lower("Hong Kong");
UPDATE civicrm_country SET name = "Hungary" WHERE lower(name) = lower("Hungary");
UPDATE civicrm_country SET name = "Iceland" WHERE lower(name) = lower("Iceland");
UPDATE civicrm_country SET name = "India" WHERE lower(name) = lower("India");
UPDATE civicrm_country SET name = "Indonesia" WHERE lower(name) = lower("Indonesia");
UPDATE civicrm_country SET name = "Iran, Islamic Republic of" WHERE lower(name) = lower("Iran, Islamic Republic of");
UPDATE civicrm_country SET name = "Iraq" WHERE lower(name) = lower("Iraq");
UPDATE civicrm_country SET name = "Ireland" WHERE lower(name) = lower("Ireland");
UPDATE civicrm_country SET name = "Israel" WHERE lower(name) = lower("Israel");
UPDATE civicrm_country SET name = "Italy" WHERE lower(name) = lower("Italy");
UPDATE civicrm_country SET name = "Jamaica" WHERE lower(name) = lower("Jamaica");
UPDATE civicrm_country SET name = "Japan" WHERE lower(name) = lower("Japan");
UPDATE civicrm_country SET name = "Jordan" WHERE lower(name) = lower("Jordan");
UPDATE civicrm_country SET name = "Kazakhstan" WHERE lower(name) = lower("Kazakhstan");
UPDATE civicrm_country SET name = "Kenya" WHERE lower(name) = lower("Kenya");
UPDATE civicrm_country SET name = "Kiribati" WHERE lower(name) = lower("Kiribati");
UPDATE civicrm_country SET name = "Korea, Democratic People's Republic of" WHERE lower(name) = lower("Korea, Democratic People's Republic of");
UPDATE civicrm_country SET name = "Korea, Republic of" WHERE lower(name) = lower("Korea, Republic of");
UPDATE civicrm_country SET name = "Kuwait" WHERE lower(name) = lower("Kuwait");
UPDATE civicrm_country SET name = "Kyrgyzstan" WHERE lower(name) = lower("Kyrgyzstan");
UPDATE civicrm_country SET name = "Lao People\'s Democratic Republic" WHERE lower(name) = lower("Lao People\'s Democratic Republic");
UPDATE civicrm_country SET name = "Latvia" WHERE lower(name) = lower("Latvia");
UPDATE civicrm_country SET name = "Lebanon" WHERE lower(name) = lower("Lebanon");
UPDATE civicrm_country SET name = "Lesotho" WHERE lower(name) = lower("Lesotho");
UPDATE civicrm_country SET name = "Liberia" WHERE lower(name) = lower("Liberia");
UPDATE civicrm_country SET name = "Libya" WHERE lower(name) = lower("Libya");
UPDATE civicrm_country SET name = "Liechtenstein" WHERE lower(name) = lower("Liechtenstein");
UPDATE civicrm_country SET name = "Lithuania" WHERE lower(name) = lower("Lithuania");
UPDATE civicrm_country SET name = "Luxembourg" WHERE lower(name) = lower("Luxembourg");
UPDATE civicrm_country SET name = "Macao" WHERE lower(name) = lower("Macao");
UPDATE civicrm_country SET name = "Macedonia, Republic of" WHERE lower(name) = lower("Macedonia, Republic of");
UPDATE civicrm_country SET name = "Madagascar" WHERE lower(name) = lower("Madagascar");
UPDATE civicrm_country SET name = "Malawi" WHERE lower(name) = lower("Malawi");
UPDATE civicrm_country SET name = "Malaysia" WHERE lower(name) = lower("Malaysia");
UPDATE civicrm_country SET name = "Maldives" WHERE lower(name) = lower("Maldives");
UPDATE civicrm_country SET name = "Mali" WHERE lower(name) = lower("Mali");
UPDATE civicrm_country SET name = "Malta" WHERE lower(name) = lower("Malta");
UPDATE civicrm_country SET name = "Marshall Islands" WHERE lower(name) = lower("Marshall Islands");
UPDATE civicrm_country SET name = "Martinique" WHERE lower(name) = lower("Martinique");
UPDATE civicrm_country SET name = "Mauritania" WHERE lower(name) = lower("Mauritania");
UPDATE civicrm_country SET name = "Mauritius" WHERE lower(name) = lower("Mauritius");
UPDATE civicrm_country SET name = "Mayotte" WHERE lower(name) = lower("Mayotte");
UPDATE civicrm_country SET name = "Mexico" WHERE lower(name) = lower("Mexico");
UPDATE civicrm_country SET name = "Micronesia, Federated States of" WHERE lower(name) = lower("Micronesia, Federated States of");
UPDATE civicrm_country SET name = "Moldova" WHERE lower(name) = lower("Moldova");
UPDATE civicrm_country SET name = "Monaco" WHERE lower(name) = lower("Monaco");
UPDATE civicrm_country SET name = "Mongolia" WHERE lower(name) = lower("Mongolia");
UPDATE civicrm_country SET name = "Montserrat" WHERE lower(name) = lower("Montserrat");
UPDATE civicrm_country SET name = "Morocco" WHERE lower(name) = lower("Morocco");
UPDATE civicrm_country SET name = "Mozambique" WHERE lower(name) = lower("Mozambique");
UPDATE civicrm_country SET name = "Namibia" WHERE lower(name) = lower("Namibia");
UPDATE civicrm_country SET name = "Nauru" WHERE lower(name) = lower("Nauru");
UPDATE civicrm_country SET name = "Nepal" WHERE lower(name) = lower("Nepal");
UPDATE civicrm_country SET name = "Netherlands" WHERE lower(name) = lower("Netherlands");
UPDATE civicrm_country SET name = "New Caledonia" WHERE lower(name) = lower("New Caledonia");
UPDATE civicrm_country SET name = "New Zealand" WHERE lower(name) = lower("New Zealand");
UPDATE civicrm_country SET name = "Nicaragua" WHERE lower(name) = lower("Nicaragua");
UPDATE civicrm_country SET name = "Niger" WHERE lower(name) = lower("Niger");
UPDATE civicrm_country SET name = "Nigeria" WHERE lower(name) = lower("Nigeria");
UPDATE civicrm_country SET name = "Niue" WHERE lower(name) = lower("Niue");
UPDATE civicrm_country SET name = "Norfolk Island" WHERE lower(name) = lower("Norfolk Island");
UPDATE civicrm_country SET name = "Northern Mariana Islands" WHERE lower(name) = lower("Northern Mariana Islands");
UPDATE civicrm_country SET name = "Norway" WHERE lower(name) = lower("Norway");
UPDATE civicrm_country SET name = "Oman" WHERE lower(name) = lower("Oman");
UPDATE civicrm_country SET name = "Pakistan" WHERE lower(name) = lower("Pakistan");
UPDATE civicrm_country SET name = "Palau" WHERE lower(name) = lower("Palau");
UPDATE civicrm_country SET name = "Palestinian Territory" WHERE lower(name) = lower("Palestinian Territory");
UPDATE civicrm_country SET name = "Panama" WHERE lower(name) = lower("Panama");
UPDATE civicrm_country SET name = "Papua New Guinea" WHERE lower(name) = lower("Papua New Guinea");
UPDATE civicrm_country SET name = "Paraguay" WHERE lower(name) = lower("Paraguay");
UPDATE civicrm_country SET name = "Peru" WHERE lower(name) = lower("Peru");
UPDATE civicrm_country SET name = "Philippines" WHERE lower(name) = lower("Philippines");
UPDATE civicrm_country SET name = "Pitcairn" WHERE lower(name) = lower("Pitcairn");
UPDATE civicrm_country SET name = "Poland" WHERE lower(name) = lower("Poland");
UPDATE civicrm_country SET name = "Portugal" WHERE lower(name) = lower("Portugal");
UPDATE civicrm_country SET name = "Puerto Rico" WHERE lower(name) = lower("Puerto Rico");
UPDATE civicrm_country SET name = "Qatar" WHERE lower(name) = lower("Qatar");
UPDATE civicrm_country SET name = "Romania" WHERE lower(name) = lower("Romania");
UPDATE civicrm_country SET name = "Russian Federation" WHERE lower(name) = lower("Russian Federation");
UPDATE civicrm_country SET name = "Rwanda" WHERE lower(name) = lower("Rwanda");
UPDATE civicrm_country SET name = "Reunion" WHERE lower(name) = lower("Reunion");
UPDATE civicrm_country SET name = "Saint Helena" WHERE lower(name) = lower("Saint Helena");
UPDATE civicrm_country SET name = "Saint Kitts and Nevis" WHERE lower(name) = lower("Saint Kitts and Nevis");
UPDATE civicrm_country SET name = "Saint Lucia" WHERE lower(name) = lower("Saint Lucia");
UPDATE civicrm_country SET name = "Saint Pierre and Miquelon" WHERE lower(name) = lower("Saint Pierre and Miquelon");
UPDATE civicrm_country SET name = "Saint Vincent and the Grenadines" WHERE lower(name) = lower("Saint Vincent and the Grenadines");
UPDATE civicrm_country SET name = "Samoa" WHERE lower(name) = lower("Samoa");
UPDATE civicrm_country SET name = "San Marino" WHERE lower(name) = lower("San Marino");
UPDATE civicrm_country SET name = "Saudi Arabia" WHERE lower(name) = lower("Saudi Arabia");
UPDATE civicrm_country SET name = "Senegal" WHERE lower(name) = lower("Senegal");
UPDATE civicrm_country SET name = "Seychelles" WHERE lower(name) = lower("Seychelles");
UPDATE civicrm_country SET name = "Sierra Leone" WHERE lower(name) = lower("Sierra Leone");
UPDATE civicrm_country SET name = "Singapore" WHERE lower(name) = lower("Singapore");
UPDATE civicrm_country SET name = "Slovakia" WHERE lower(name) = lower("Slovakia");
UPDATE civicrm_country SET name = "Slovenia" WHERE lower(name) = lower("Slovenia");
UPDATE civicrm_country SET name = "Solomon Islands" WHERE lower(name) = lower("Solomon Islands");
UPDATE civicrm_country SET name = "Somalia" WHERE lower(name) = lower("Somalia");
UPDATE civicrm_country SET name = "South Africa" WHERE lower(name) = lower("South Africa");
UPDATE civicrm_country SET name = "South Georgia and the South Sandwich Islands" WHERE lower(name) = lower("South Georgia and the South Sandwich Islands");
UPDATE civicrm_country SET name = "Spain" WHERE lower(name) = lower("Spain");
UPDATE civicrm_country SET name = "Sri Lanka" WHERE lower(name) = lower("Sri Lanka");
UPDATE civicrm_country SET name = "Sudan" WHERE lower(name) = lower("Sudan");
UPDATE civicrm_country SET name = "Suriname" WHERE lower(name) = lower("Suriname");
UPDATE civicrm_country SET name = "Svalbard and Jan Mayen" WHERE lower(name) = lower("Svalbard and Jan Mayen");
UPDATE civicrm_country SET name = "Swaziland" WHERE lower(name) = lower("Swaziland");
UPDATE civicrm_country SET name = "Sweden" WHERE lower(name) = lower("Sweden");
UPDATE civicrm_country SET name = "Switzerland" WHERE lower(name) = lower("Switzerland");
UPDATE civicrm_country SET name = "Syrian Arab Republic" WHERE lower(name) = lower("Syrian Arab Republic");
UPDATE civicrm_country SET name = "Sao Tome and Principe" WHERE lower(name) = lower("Sao Tome and Principe");
UPDATE civicrm_country SET name = "Taiwan" WHERE lower(name) = lower("Taiwan");
UPDATE civicrm_country SET name = "Tajikistan" WHERE lower(name) = lower("Tajikistan");
UPDATE civicrm_country SET name = "Tanzania, United Republic of" WHERE lower(name) = lower("Tanzania, United Republic of");
UPDATE civicrm_country SET name = "Thailand" WHERE lower(name) = lower("Thailand");
UPDATE civicrm_country SET name = "Bahamas" WHERE lower(name) = lower("Bahamas");
UPDATE civicrm_country SET name = "Gambia" WHERE lower(name) = lower("Gambia");
UPDATE civicrm_country SET name = "Togo" WHERE lower(name) = lower("Togo");
UPDATE civicrm_country SET name = "Tokelau" WHERE lower(name) = lower("Tokelau");
UPDATE civicrm_country SET name = "Tonga" WHERE lower(name) = lower("Tonga");
UPDATE civicrm_country SET name = "Trinidad and Tobago" WHERE lower(name) = lower("Trinidad and Tobago");
UPDATE civicrm_country SET name = "Tunisia" WHERE lower(name) = lower("Tunisia");
UPDATE civicrm_country SET name = "Turkey" WHERE lower(name) = lower("Turkey");
UPDATE civicrm_country SET name = "Turkmenistan" WHERE lower(name) = lower("Turkmenistan");
UPDATE civicrm_country SET name = "Turks and Caicos Islands" WHERE lower(name) = lower("Turks and Caicos Islands");
UPDATE civicrm_country SET name = "Tuvalu" WHERE lower(name) = lower("Tuvalu");
UPDATE civicrm_country SET name = "Uganda" WHERE lower(name) = lower("Uganda");
UPDATE civicrm_country SET name = "Ukraine" WHERE lower(name) = lower("Ukraine");
UPDATE civicrm_country SET name = "United Arab Emirates" WHERE lower(name) = lower("United Arab Emirates");
UPDATE civicrm_country SET name = "United Kingdom" WHERE lower(name) = lower("United Kingdom");
UPDATE civicrm_country SET name = "United States Minor Outlying Islands" WHERE lower(name) = lower("United States Minor Outlying Islands");
UPDATE civicrm_country SET name = "United States" WHERE lower(name) = lower("United States");
UPDATE civicrm_country SET name = "Uruguay" WHERE lower(name) = lower("Uruguay");
UPDATE civicrm_country SET name = "Uzbekistan" WHERE lower(name) = lower("Uzbekistan");
UPDATE civicrm_country SET name = "Vanuatu" WHERE lower(name) = lower("Vanuatu");
UPDATE civicrm_country SET name = "Venezuela" WHERE lower(name) = lower("Venezuela");
UPDATE civicrm_country SET name = "Viet Nam" WHERE lower(name) = lower("Viet Nam");
UPDATE civicrm_country SET name = "Virgin Islands, British" WHERE lower(name) = lower("Virgin Islands, British");
UPDATE civicrm_country SET name = "Wallis and Futuna" WHERE lower(name) = lower("Wallis and Futuna");
UPDATE civicrm_country SET name = "Western Sahara" WHERE lower(name) = lower("Western Sahara");
UPDATE civicrm_country SET name = "Yemen" WHERE lower(name) = lower("Yemen");
UPDATE civicrm_country SET name = "Serbia and Montenegro" WHERE lower(name) = lower("Serbia and Montenegro");
UPDATE civicrm_country SET name = "Zambia" WHERE lower(name) = lower("Zambia");
UPDATE civicrm_country SET name = "Zimbabwe" WHERE lower(name) = lower("Zimbabwe");
UPDATE civicrm_country SET name = "Åland Islands" WHERE lower(name) = lower("Åland Islands");
UPDATE civicrm_country SET name = "Serbia" WHERE lower(name) = lower("Serbia");
UPDATE civicrm_country SET name = "Montenegro" WHERE lower(name) = lower("Montenegro");
UPDATE civicrm_country SET name = "Jersey" WHERE lower(name) = lower("Jersey");
UPDATE civicrm_country SET name = "Guernsey" WHERE lower(name) = lower("Guernsey");
UPDATE civicrm_country SET name = "Isle of Man" WHERE lower(name) = lower("Isle of Man");
UPDATE civicrm_country SET name = "South Sudan" WHERE lower(name) = lower("South Sudan");
UPDATE civicrm_country SET name = "Curaçao" WHERE lower(name) = lower("Curaçao");
UPDATE civicrm_country SET name = "Sint Maarten (Dutch Part)" WHERE lower(name) = lower("Sint Maarten (Dutch Part)");
UPDATE civicrm_country SET name = "Bonaire, Saint Eustatius and Saba" WHERE lower(name) = lower("Bonaire, Saint Eustatius and Saba");
UPDATE civicrm_country SET name = "Kosovo" WHERE lower(name) = lower("Kosovo");
UPDATE civicrm_country SET name = "Saint Barthélemy" WHERE lower(name) = lower("Saint Barthélemy");
UPDATE civicrm_country SET name = "Saint Martin (French part)" WHERE lower(name) = lower("Saint Martin (French part)");
UPDATE civicrm_country SET name = "Afghanistan" WHERE lower(name) = lower("Afghanistan");
UPDATE civicrm_country SET name = "Albania" WHERE lower(name) = lower("Albania");
UPDATE civicrm_country SET name = "Algeria" WHERE lower(name) = lower("Algeria");
