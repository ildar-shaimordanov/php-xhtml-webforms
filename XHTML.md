# Hierarchical structure of XHTML elements 

* XHTML_Common
  * XHTML_Element
    * XHTML_Text
    * XHTML_Form
      * _XHTML_QuickForm_
    * XHTML_Control
      * XHTML_Input
        * XHTML_Captcha
        * XHTML_Spinbox
        * XHTML_Togglebox
        * XHTML_Comparebox
        * _XHTML_QuickForm_*_
      * XHTML_Textarea
        * _XHTML_QuickForm_Textarea_
      * XHTML_Select
        * _XHTML_QuickForm_Select_
      * XHTML_Button
        * _XHTML_QuickForm_Xbutton_
  * XHTML_Group
    * XHTML_Listbox
    * XHTML_Textbox
    * XHTML_Comparebox
    * XHTML_Wizard
    * XHTML_Tab


# List of available metas

* `meta:no-overwrite`, `meta:no-overwrite="no-overwrite"`

  If it is sets the actual submitted value will not overwrite the default value of html-controls.

* `meta:label="value"`

  The "value" specifies a text nearby an html-control describing the last one.

* `meta:source="value"`

  `value` specifies a callback function to initialize the control from datasources. The source accpts the single parameter as reference to the control.

* `meta:submit="value"`

  `value` specifies a list and an order of handled submitted data.

  There are available submits:

  * `post`
  * `get`
  * `files`
  * `cookie`

  Omitted meta means `meta:submit="post get"`, i.e. the first handled submit is `POST`, and after `GET`.

  There is special case of submit such "none" meaning no submit and no handling. 

* `meta:filter="value"`

  `value` specifies vaious filters applied in the order of their appearance in the list.

* `meta:validator="value"`

  `value` specifies various validators and parameters for html-controls. Each validator is applied in the order of their appearance in the list.

  There are available validators:

  * `required` - (value is not empty) 

  * `alphabetical` - (value must contain letters only) 
  * `alphanumeric` - (value must contain leters or number only) 
  * `integer` - (value must be integer) 
  * `float` - (value must be float) 

  * `email` - (value must contain a valid e-mail) 
  * `ip` - (value must contain a valid ip-address) 
  * `url` - (value must contain a valid url) 
  * `varname` - (value must contain a valid variable name) 

  * `length` - (value must have exact number of characters) 
  * `minlength` - (value must have more than given number of characters) 
  * `maxlength` - (value must not exceed given number of characters) 
  * `rangelength` - (value must have between min and max characters) 

  * `range` - (value must be numeric between min and max numbers) 
  * `list` - (value must be one of values from the list) 

  * `uploaded` - (the file must be uploaded - is the same as `required`) 
  * `minfilesize` - (the file size must be equal or exceed the given number of bytes) 
  * `maxfilesize` - (the file size must not exceed the given number of bytes) 
  * `mimetype` - (the file must have a correct MIME type) 

* `meta:message="value"`

  `value` specifies a message to be appeared when error occures 

# Links

Dmitrijj Borodin

* http://php.spb.ru/phpLoginForm/

PEAR

* http://pear.php.net/package/HTML_Form
* http://pear.php.net/package/HTML_QuickForm
* http://pear.php.net/package/HTML_QuickForm2

dkLab

* http://dklab.ru/lib/HTML_FormPersister/
* http://dklab.ru/lib/HTML_MetaForm/

Debugger

* http://svn.debugger.ru/wsvn/PHP%20projects/SExy%20Forms/

Others

* http://www.html-form-guide.com/php-form/php-form-validation.html
* http://phpformgen.sourceforge.net/
* http://www.myscript.ru/fileout-1377.html
* http://forum.dklab.ru/viewtopic.php?p=63972
* http://www.formassembly.com/wForms/
* http://stefangabos.blogspot.com/2006/05/zebra-php-framework-downloads.html#phphtmlform
