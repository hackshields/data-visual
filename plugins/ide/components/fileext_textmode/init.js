!function(e,n){var d=null;n(function(){codiad.fileext_textmode.init()}),e.codiad.fileext_textmode={dialog:"components/fileext_textmode/dialog.php",controller:"components/fileext_textmode/controller.php",availableTextModes:[],init:function(){(d=this).initEditorFileExtensionTextModes(),amplify.subscribe("settings.dialog.save",function(){0!==n("#FileExtTextModeDiv:visible").length&&d.sendForm()})},initEditorFileExtensionTextModes:function(){n.get(this.controller,{action:"GetFileExtTextModes"},this.setEditorFileExtensionTextModes)},setEditorFileExtensionTextModes:function(e){if(resp=n.parseJSON(e),"error"!=resp.status&&null!=resp.extensions){for(i in codiad.editor.clearFileExtensionTextMode(),resp.extensions)codiad.editor.addFileExtensionTextMode(i,resp.extensions[i]);null!=resp.textModes&&resp.textModes!=[]&&(d.availableTextModes=resp.textModes),amplify.publish("fileext_textmode.loadedExtensions")}d.showStatus(e)},formWidth:400,open:function(){codiad.modal.load(this.formWidth,this.dialog+"?action=fileextension_textmode_form"),codiad.modal.hideOverlay()},sendForm:function(){for(var e=n("#FileExtTextModeDiv"),t=e.find(".FileExtension"),o={"extension[]":[],"textMode[]":[]},i=0;i<t.size();++i)o["extension[]"].push(t[i].value);var s=e.find(".textMode");for(i=0;i<s.size();++i)o["textMode[]"].push(s[i].value);n.post(this.controller+"?action=FileExtTextModeForm",o,d.setEditorFileExtensionTextModes)},addFieldToForm:function(){var e=n("#FileExtTextModeTable"),t=n("#FileExtTextModeTableTbody"),o='<tr><td><input class="FileExtension" type="text" name="extension[]" value="" /></td>';o+='<td><select name="textMode[]" class="textMode">';for(var i=0;i<this.availableTextModes.length;++i)o+="<option>"+this.availableTextModes[i]+"</option>";o+="</select></td></tr>",t.append(o),e.scrollTop(1e6)},showStatus:function(e){if(null!=(e=n.parseJSON(e)).status&&""!=e.status&&null!=e.msg&&""!=e.message)switch(e.status){case"success":codiad.message.success(e.msg);break;case"error":codiad.message.error(e.msg);break;case"notice":codiad.message.notice(e.msg)}}}}(this,jQuery);