INSERT IGNORE INTO `#__rsform_forms` SET
`FormId`=1,
`FormName`='RSformPro example',
`FormLayout`='<h2>{global:formtitle}</h2>\r\n{error}\r\n<!-- Do not remove this ID, it is used to identify the page so that the pagination script can work correctly -->\r\n<fieldset class="formHorizontal formContainer" id="rsform_1_page_0">\r\n	<div class="rsform-block rsform-block-header">\r\n		<div class="formControlLabel">{Header:caption}</div>\r\n		<div class="formControls">\r\n		<div class="formBody">{Header:body}<span class="formValidation">{Header:validation}</span></div>\r\n		<p class="formDescription">{Header:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-fullname">\r\n		<div class="formControlLabel">{FullName:caption}<strong class="formRequired">(*)</strong></div>\r\n		<div class="formControls">\r\n		<div class="formBody">{FullName:body}<span class="formValidation">{FullName:validation}</span></div>\r\n		<p class="formDescription">{FullName:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-email">\r\n		<div class="formControlLabel">{Email:caption}<strong class="formRequired">(*)</strong></div>\r\n		<div class="formControls">\r\n		<div class="formBody">{Email:body}<span class="formValidation">{Email:validation}</span></div>\r\n		<p class="formDescription">{Email:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-companysize">\r\n		<div class="formControlLabel">{CompanySize:caption}<strong class="formRequired">(*)</strong></div>\r\n		<div class="formControls">\r\n		<div class="formBody">{CompanySize:body}<span class="formValidation">{CompanySize:validation}</span></div>\r\n		<p class="formDescription">{CompanySize:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-position">\r\n		<div class="formControlLabel">{Position:caption}<strong class="formRequired">(*)</strong></div>\r\n		<div class="formControls">\r\n		<div class="formBody">{Position:body}<span class="formValidation">{Position:validation}</span></div>\r\n		<p class="formDescription">{Position:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-contactby">\r\n		<div class="formControlLabel">{ContactBy:caption}</div>\r\n		<div class="formControls">\r\n		<div class="formBody">{ContactBy:body}<span class="formValidation">{ContactBy:validation}</span></div>\r\n		<p class="formDescription">{ContactBy:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-contactwhen">\r\n		<div class="formControlLabel">{ContactWhen:caption}<strong class="formRequired">(*)</strong></div>\r\n		<div class="formControls">\r\n		<div class="formBody">{ContactWhen:body}<span class="formValidation">{ContactWhen:validation}</span></div>\r\n		<p class="formDescription">{ContactWhen:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-submit">\r\n		<div class="formControlLabel">{Submit:caption}</div>\r\n		<div class="formControls">\r\n		<div class="formBody">{Submit:body}<span class="formValidation">{Submit:validation}</span></div>\r\n		<p class="formDescription">{Submit:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-footer">\r\n		<div class="formControlLabel">{Footer:caption}</div>\r\n		<div class="formControls">\r\n		<div class="formBody">{Footer:body}<span class="formValidation">{Footer:validation}</span></div>\r\n		<p class="formDescription">{Footer:description}</p>\r\n		</div>\r\n	</div>\r\n</fieldset>\r\n',
`FormLayoutName`='responsive',
`FormLayoutAutogenerate`=1,
`FormTitle`='RSForm! Pro example',
`Published`=1,
`Lang`='',
`ReturnUrl`='',
`Thankyou`='<p>Dear {FullName:value},</p><p> thank you for your submission. One of our staff members will contact you by  {ContactBy:value} as soon as possible.</p>',
`UserEmailText`='<p>Dear {FullName:value},</p><p> we received your contact request. Someone will get back to you by {ContactBy:value} soon. </p>',
`UserEmailTo`='{Email:value}',
`UserEmailFrom`='your@email.com',
`UserEmailFromName`='Your Company',
`UserEmailSubject`='Contact confirmation',
`UserEmailMode`=1,
`AdminEmailText`='<p>Customize this e-mail also. You will receive it as administrator. </p><p>{FullName:caption}:{FullName:value}<br />\n{Email:caption}:{Email:value}<br />\n{CompanySize:caption}:{CompanySize:value}<br />\n{Position:caption}:{Position:value}<br />\n{ContactBy:caption}:{ContactBy:value}<br />\n{ContactWhen:caption}:{ContactWhen:value}</p>',
`AdminEmailTo`='youradminemail@email.com',
`AdminEmailFrom`='{Email:value}',
`AdminEmailFromName`='Your Company',
`AdminEmailSubject`='Contact',
`AdminEmailMode`=1,
`Keepdata`=1,
`MultipleSeparator`=', ';

INSERT IGNORE INTO `#__rsform_components`
(`ComponentId`, `FormId`, `ComponentTypeId`, `Order`, `Published`)
VALUES
(1, 1, 1, 2, 1),
(2, 1, 10, 1, 1),
(3, 1, 1, 3, 1),
(4, 1, 3, 4, 1),
(5, 1, 5, 5, 1),
(6, 1, 4, 6, 1),
(7, 1, 6, 7, 1),
(8, 1, 13, 8, 1),
(9, 1, 10, 9, 1);

INSERT IGNORE INTO `#__rsform_properties` (`ComponentId`, `PropertyName`, `PropertyValue`)
VALUES
(1, 'NAME', 'FullName'),
(1, 'CAPTION', 'Full Name'),
(1, 'REQUIRED', 'YES'),
(1, 'SIZE', '20'),
(1, 'MAXSIZE', ''),
(1, 'VALIDATIONRULE', 'none'),
(1, 'VALIDATIONMESSAGE', 'Please type your full name.'),
(1, 'ADDITIONALATTRIBUTES', ''),
(1, 'DEFAULTVALUE', ''),
(1, 'DESCRIPTION', ''),
(2, 'NAME', 'Header'),
(2, 'TEXT', '<b>This text describes the form. It is added using the Free Text component</b>. HTML code can be added directly here.'),
(3, 'NAME', 'Email'),
(3, 'CAPTION', 'E-mail'),
(3, 'REQUIRED', 'YES'),
(3, 'SIZE', '20'),
(3, 'MAXSIZE', ''),
(3, 'VALIDATIONRULE', 'email'),
(3, 'VALIDATIONMESSAGE', 'Invalid email address.'),
(3, 'ADDITIONALATTRIBUTES', ''),
(3, 'DEFAULTVALUE', ''),
(3, 'DESCRIPTION', ''),
(4, 'NAME', 'CompanySize'),
(4, 'CAPTION', 'Number of Employees'),
(4, 'SIZE', ''),
(4, 'MULTIPLE', 'NO'),
(4, 'ITEMS', '|Please Select[c]\n1-20\n21-50\n51-100\n>100|More than 100'),
(4, 'REQUIRED', 'YES'),
(4, 'ADDITIONALATTRIBUTES', ''),
(4, 'DESCRIPTION', ''),
(4, 'VALIDATIONMESSAGE', 'Please tell us how big is your company.'),
(5, 'NAME', 'Position'),
(5, 'CAPTION', 'Position'),
(5, 'ITEMS', 'CEO\nCFO\nCTO\nHR[c]'),
(5, 'FLOW', 'HORIZONTAL'),
(5, 'REQUIRED', 'YES'),
(5, 'ADDITIONALATTRIBUTES', ''),
(5, 'DESCRIPTION', ''),
(5, 'VALIDATIONMESSAGE', 'Please specify your position in the company'),
(6, 'NAME', 'ContactBy'),
(6, 'CAPTION', 'How should we contact you?'),
(6, 'ITEMS', 'E-mail[c]\nPhone\nNewsletter[c]\nMail'),
(6, 'FLOW', 'HORIZONTAL'),
(6, 'REQUIRED', 'NO'),
(6, 'ADDITIONALATTRIBUTES', ''),
(6, 'DESCRIPTION', ''),
(6, 'VALIDATIONMESSAGE', ''),
(7, 'NAME', 'ContactWhen'),
(7, 'CAPTION', 'When would you like to be contacted?'),
(7, 'REQUIRED', 'YES'),
(7, 'DATEFORMAT', 'dd.mm.yyyy'),
(7, 'CALENDARLAYOUT', 'POPUP'),
(7, 'ADDITIONALATTRIBUTES', ''),
(7, 'READONLY', 'YES'),
(7, 'POPUPLABEL', '...'),
(7, 'DESCRIPTION', ''),
(7, 'VALIDATIONMESSAGE', 'Please select a date when we should contact you.'),
(8, 'NAME', 'Submit'),
(8, 'LABEL', 'Submit'),
(8, 'CAPTION', ''),
(8, 'RESET', 'YES'),
(8, 'RESETLABEL', 'Reset'),
(8, 'ADDITIONALATTRIBUTES', ''),
(9, 'NAME', 'Footer'),
(9, 'TEXT', 'This form is an example. Please check our knowledgebase for articles related to how you should build your form. Articles are updated daily. <a href="http://www.rsjoomla.com/" target="_blank">http://www.rsjoomla.com/</a>');

INSERT IGNORE INTO `#__rsform_forms` (`FormId`, `FormName`, `FormLayout`, `FormLayoutName`, `FormLayoutAutogenerate`, `CSS`, `JS`, `FormTitle`, `Published`, `Lang`, `ReturnUrl`, `ShowThankyou`, `Thankyou`, `UserEmailText`, `UserEmailTo`, `UserEmailCC`, `UserEmailBCC`, `UserEmailFrom`, `UserEmailReplyTo`, `UserEmailFromName`, `UserEmailSubject`, `UserEmailMode`, `UserEmailAttach`, `UserEmailAttachFile`, `AdminEmailText`, `AdminEmailTo`, `AdminEmailCC`, `AdminEmailBCC`, `AdminEmailFrom`, `AdminEmailReplyTo`, `AdminEmailFromName`, `AdminEmailSubject`, `AdminEmailMode`, `ScriptProcess`, `ScriptProcess2`, `ScriptDisplay`, `UserEmailScript`, `AdminEmailScript`, `MetaTitle`, `MetaDesc`, `MetaKeywords`, `Required`, `ErrorMessage`, `MultipleSeparator`, `TextareaNewLines`,`Keepdata`) VALUES
(2, 'RSformPro Multipage example', '<h2>{global:formtitle}</h2>\r\n{error}\r\n<!-- Do not remove this ID, it is used to identify the page so that the pagination script can work correctly -->\r\n<fieldset class="formHorizontal formContainer" id="rsform_2_page_0">\r\n	<div class="rsform-block rsform-block-header">\r\n		<div class="formControlLabel">{Header:caption}</div>\r\n		<div class="formControls">\r\n		<div class="formBody">{Header:body}<span class="formValidation">{Header:validation}</span></div>\r\n		<p class="formDescription">{Header:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-fullname">\r\n		<div class="formControlLabel">{FullName:caption}<strong class="formRequired">(*)</strong></div>\r\n		<div class="formControls">\r\n		<div class="formBody">{FullName:body}<span class="formValidation">{FullName:validation}</span></div>\r\n		<p class="formDescription">{FullName:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-email">\r\n		<div class="formControlLabel">{Email:caption}<strong class="formRequired">(*)</strong></div>\r\n		<div class="formControls">\r\n		<div class="formBody">{Email:body}<span class="formValidation">{Email:validation}</span></div>\r\n		<p class="formDescription">{Email:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-page1">\r\n		<div class="formControlLabel">&nbsp;</div>\r\n		<div class="formControls">\r\n		<div class="formBody">{Page1:body}</div>\r\n		</div>\r\n	</div>\r\n	</fieldset>\r\n<!-- Do not remove this ID, it is used to identify the page so that the pagination script can work correctly -->\r\n<fieldset class="formHorizontal formContainer" id="rsform_2_page_1">\r\n	<div class="rsform-block rsform-block-companyheader">\r\n		<div class="formControlLabel">{CompanyHeader:caption}</div>\r\n		<div class="formControls">\r\n		<div class="formBody">{CompanyHeader:body}<span class="formValidation">{CompanyHeader:validation}</span></div>\r\n		<p class="formDescription">{CompanyHeader:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-companysize">\r\n		<div class="formControlLabel">{CompanySize:caption}<strong class="formRequired">(*)</strong></div>\r\n		<div class="formControls">\r\n		<div class="formBody">{CompanySize:body}<span class="formValidation">{CompanySize:validation}</span></div>\r\n		<p class="formDescription">{CompanySize:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-position">\r\n		<div class="formControlLabel">{Position:caption}<strong class="formRequired">(*)</strong></div>\r\n		<div class="formControls">\r\n		<div class="formBody">{Position:body}<span class="formValidation">{Position:validation}</span></div>\r\n		<p class="formDescription">{Position:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-page2">\r\n		<div class="formControlLabel">&nbsp;</div>\r\n		<div class="formControls">\r\n		<div class="formBody">{Page2:body}</div>\r\n		</div>\r\n	</div>\r\n	</fieldset>\r\n<!-- Do not remove this ID, it is used to identify the page so that the pagination script can work correctly -->\r\n<fieldset class="formHorizontal formContainer" id="rsform_2_page_2">\r\n	<div class="rsform-block rsform-block-contactheader">\r\n		<div class="formControlLabel">{ContactHeader:caption}</div>\r\n		<div class="formControls">\r\n		<div class="formBody">{ContactHeader:body}<span class="formValidation">{ContactHeader:validation}</span></div>\r\n		<p class="formDescription">{ContactHeader:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-contactby">\r\n		<div class="formControlLabel">{ContactBy:caption}</div>\r\n		<div class="formControls">\r\n		<div class="formBody">{ContactBy:body}<span class="formValidation">{ContactBy:validation}</span></div>\r\n		<p class="formDescription">{ContactBy:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-contactwhen">\r\n		<div class="formControlLabel">{ContactWhen:caption}<strong class="formRequired">(*)</strong></div>\r\n		<div class="formControls">\r\n		<div class="formBody">{ContactWhen:body}<span class="formValidation">{ContactWhen:validation}</span></div>\r\n		<p class="formDescription">{ContactWhen:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-submit">\r\n		<div class="formControlLabel">{Submit:caption}</div>\r\n		<div class="formControls">\r\n		<div class="formBody">{Submit:body}<span class="formValidation">{Submit:validation}</span></div>\r\n		<p class="formDescription">{Submit:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-footer">\r\n		<div class="formControlLabel">{Footer:caption}</div>\r\n		<div class="formControls">\r\n		<div class="formBody">{Footer:body}<span class="formValidation">{Footer:validation}</span></div>\r\n		<p class="formDescription">{Footer:description}</p>\r\n		</div>\r\n	</div>\r\n</fieldset>\r\n', 'responsive', 1, '', '', 'RSForm! Pro Multipage example', 1, '', '', 0, '<p>Dear {FullName:value},</p><p> thank you for your submission. One of our staff members will contact you by  {ContactBy:value} as soon as possible.</p>', '<p>Dear {FullName:value},</p><p> we received your contact request. Someone will get back to you by {ContactBy:value} soon. </p>', '{Email:value}', '', '', 'your@email.com', '', 'Your Company', 'Contact confirmation', 1, 0, '', '<p>Customize this e-mail also. You will receive it as administrator. </p><p>{FullName:caption}:{FullName:value}<br />\n{Email:caption}:{Email:value}<br />\n{CompanySize:caption}:{CompanySize:value}<br />\n{Position:caption}:{Position:value}<br />\n{ContactBy:caption}:{ContactBy:value}<br />\n{ContactWhen:caption}:{ContactWhen:value}</p>', 'youradminemail@email.com', '', '', '{Email:value}', '', 'Your Company', 'Contact', 1, '', '', '', '', '', 0, 'This is the meta description of your form. You can use it for SEO purposes.', 'rsform, contact, form, joomla', '(*)', '<p class="formRed">Please complete all required fields!</p>', ', ', 1,1);

INSERT IGNORE INTO `#__rsform_components` (`ComponentId`, `FormId`, `ComponentTypeId`, `Order`, `Published`) VALUES
(10, 2, 1, 2, 1),
(11, 2, 10, 1, 1),
(12, 2, 1, 3, 1),
(13, 2, 3, 6, 1),
(14, 2, 5, 7, 1),
(15, 2, 4, 10, 1),
(16, 2, 6, 11, 1),
(17, 2, 13, 12, 1),
(18, 2, 10, 13, 1),
(19, 2, 41, 4, 1),
(20, 2, 41, 8, 1),
(21, 2, 10, 5, 1),
(22, 2, 10, 9, 1);

INSERT IGNORE INTO `#__rsform_properties` (`ComponentId`, `PropertyName`, `PropertyValue`) VALUES
(10, 'NAME', 'FullName'),
(10, 'CAPTION', 'Full Name'),
(10, 'REQUIRED', 'YES'),
(10, 'SIZE', '20'),
(10, 'MAXSIZE', ''),
(10, 'VALIDATIONRULE', 'none'),
(10, 'VALIDATIONMESSAGE', 'Please type your full name.'),
(10, 'ADDITIONALATTRIBUTES', ''),
(10, 'DEFAULTVALUE', ''),
(10, 'DESCRIPTION', ''),
(10, 'VALIDATIONEXTRA', ''),
(11, 'NAME', 'Header'),
(11, 'TEXT', '<b>This text describes the form. It is added using the Free Text component</b>. HTML code can be added directly here.'),
(12, 'NAME', 'Email'),
(12, 'CAPTION', 'E-mail'),
(12, 'REQUIRED', 'YES'),
(12, 'SIZE', '20'),
(12, 'MAXSIZE', ''),
(12, 'VALIDATIONRULE', 'email'),
(12, 'VALIDATIONMESSAGE', 'Invalid email address.'),
(12, 'ADDITIONALATTRIBUTES', ''),
(12, 'DEFAULTVALUE', ''),
(12, 'DESCRIPTION', ''),
(12, 'VALIDATIONEXTRA', ''),
(13, 'NAME', 'CompanySize'),
(13, 'CAPTION', 'Number of Employees'),
(13, 'SIZE', ''),
(13, 'MULTIPLE', 'NO'),
(13, 'ITEMS', '|Please Select[c]\n1-20\n21-50\n51-100\n>100|More than 100'),
(13, 'REQUIRED', 'YES'),
(13, 'ADDITIONALATTRIBUTES', ''),
(13, 'DESCRIPTION', ''),
(13, 'VALIDATIONMESSAGE', 'Please tell us how big is your company.'),
(14, 'NAME', 'Position'),
(14, 'CAPTION', 'Position'),
(14, 'ITEMS', 'CEO\nCFO\nCTO\nHR[c]'),
(14, 'FLOW', 'HORIZONTAL'),
(14, 'REQUIRED', 'YES'),
(14, 'ADDITIONALATTRIBUTES', ''),
(14, 'DESCRIPTION', ''),
(14, 'VALIDATIONMESSAGE', 'Please specify your position in the company'),
(15, 'NAME', 'ContactBy'),
(15, 'CAPTION', 'How should we contact you?'),
(15, 'ITEMS', 'E-mail[c]\nPhone\nNewsletter[c]\nMail'),
(15, 'FLOW', 'HORIZONTAL'),
(15, 'REQUIRED', 'NO'),
(15, 'ADDITIONALATTRIBUTES', ''),
(15, 'DESCRIPTION', ''),
(15, 'VALIDATIONMESSAGE', ''),
(16, 'NAME', 'ContactWhen'),
(16, 'CAPTION', 'When would you like to be contacted?'),
(16, 'REQUIRED', 'YES'),
(16, 'DATEFORMAT', 'dd.mm.yyyy'),
(16, 'CALENDARLAYOUT', 'POPUP'),
(16, 'ADDITIONALATTRIBUTES', ''),
(16, 'READONLY', 'YES'),
(16, 'POPUPLABEL', '...'),
(16, 'DESCRIPTION', ''),
(16, 'VALIDATIONMESSAGE', 'Please select a date when we should contact you.'),
(17, 'NAME', 'Submit'),
(17, 'LABEL', 'Submit'),
(17, 'CAPTION', ''),
(17, 'RESET', 'YES'),
(17, 'RESETLABEL', 'Reset'),
(17, 'ADDITIONALATTRIBUTES', ''),
(18, 'NAME', 'Footer'),
(18, 'TEXT', 'This form is an example. Please check our knowledgebase for articles related to how you should build your form. Articles are updated daily. <a href="http://www.rsjoomla.com/" target="_blank">http://www.rsjoomla.com/</a>'),
(19, 'NAME', 'Page1'),
(19, 'NEXTBUTTON', 'Next >'),
(19, 'PREVBUTTON', 'Prev'),
(19, 'ADDITIONALATTRIBUTES', ''),
(20, 'NAME', 'Page2'),
(20, 'NEXTBUTTON', 'Next >'),
(20, 'PREVBUTTON', 'Prev'),
(20, 'ADDITIONALATTRIBUTES', ''),
(21, 'NAME', 'CompanyHeader'),
(21, 'TEXT', 'Please tell us a little about your company.'),
(22, 'NAME', 'ContactHeader'),
(22, 'TEXT', 'Please let us know how and when to contact you.');