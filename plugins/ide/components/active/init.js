!function(t,c){var h=ace.require("ace/edit_session").EditSession,d=ace.require("ace/undomanager").UndoManager,p=t.codiad;c(function(){p.active.init()}),p.active={controller:"components/active/controller.php",sessions:{},history:[],isOpen:function(t){return!!this.sessions[t]},open:function(e,s,a,o,n){void 0===n&&(n=!0);var l=this;if(this.isOpen(e))n&&this.focus(e);else{var t=p.filemanager.getExtension(e),r=p.editor.selectMode(t);c.loadScript("https://assets.dbface.com/libs/ace/mode-"+r+".js",function(){var t=l.checkDraft(e);t&&(s=t,p.message.success(i18n("Recovered unsaved content for: ")+e));var i=new h(s);i.setMode("ace/mode/"+r),i.setUndoManager(new d),i.path=e,i.serverMTime=a,(l.sessions[e]=i).untainted=s.slice(0),!o&&n&&p.editor.setSession(i),l.add(e,i,n),amplify.publish("active.onOpen",e)})}},init:function(){var s=this;s.initTabDropdownMenu(),s.updateTabDropdownVisibility(),c("#list-active-files a").live("click",function(t){t.stopPropagation(),s.focus(c(this).parent("li").attr("data-path"))}),c("#dropdown-list-active-files a").live("click",function(t){1==t.which&&s.focus(c(this).parent("li").attr("data-path"))}),c("#tab-list-active-files li.tab-item>a.label").live("mousedown",function(t){1==t.which&&(t.stopPropagation(),s.focus(c(this).parent("li").attr("data-path")))}),c("#list-active-files a>span").live("click",function(t){t.stopPropagation(),s.remove(c(this).parent("a").parent("li").attr("data-path"))}),c("#dropdown-list-active-files a>span").live("click",function(t){t.stopPropagation();var i=s.getPath(),e=c(this).parents("li").attr("data-path");s.remove(e),null!==i&&i!==e&&s.focus(i),s.updateTabDropdownVisibility()}),c("#tab-list-active-files a.close").live("click",function(t){t.stopPropagation();var i=s.getPath(),e=c(this).parent("li").attr("data-path");s.remove(e),null!==i&&i!==e&&s.focus(i),s.updateTabDropdownVisibility()}),c("#dropdown-list-active-files li").live("mouseup",function(t){if(2==t.which){t.stopPropagation();var i=s.getPath(),e=c(this).attr("data-path");s.remove(e),null!==i&&i!==e&&s.focus(i),s.updateTabDropdownVisibility()}}),c(".tab-item").live("mouseup",function(t){if(2==t.which){t.stopPropagation();var i=s.getPath(),e=c(this).attr("data-path");s.remove(e),null!==i&&i!==e&&s.focus(i),s.updateTabDropdownVisibility()}}),c("#list-active-files").sortable({placeholder:"active-sort-placeholder",tolerance:"intersect",start:function(t,i){i.placeholder.height(i.item.height())}}),c("#dropdown-list-active-files").sortable({axis:"y",tolerance:"pointer",start:function(t,i){i.placeholder.height(i.item.height())}}),c("#tab-list-active-files").sortable({items:"> li",axis:"x",tolerance:"pointer",containment:"parent",start:function(t,i){i.placeholder.css("background","transparent"),i.helper.css("width","200px")},stop:function(t,i){i.item.css("z-index",""),i.item.css("position","")}}),c("#tab-list-active-files").data("sortable").floating=!0,c.get(s.controller+"?action=list",function(t){var i=p.jsend.parse(t);null!==i&&c.each(i,function(t,i){p.filemanager.openFile(i.path,i.focused)})}),window.onbeforeunload=function(t){if(0<c("#list-active-files li.changed").length){t=t||window.event;var i=i18n("You have unsaved files.");return t&&(t.returnValue=i),i}}},checkDraft:function(t){var i=localStorage.getItem(t);return null!==i&&i},removeDraft:function(t){localStorage.removeItem(t)},getPath:function(){try{return p.editor.getActive().getSession().path}catch(t){return null}},check:function(t){c.get(this.controller+"?action=check&path="+encodeURIComponent(t),function(t){p.jsend.parse(t)})},add:function(t,i,e){void 0===e&&(e=!0);var s=this.createListThumb(t);if(i.listThumb=s,c("#list-active-files").append(s),this.isTabListOverflowed(!0)){var a=c("#tab-list-active-files li:first-child");this.moveTabToDropdownMenu(a)}var o=this.createTabThumb(t);c("#tab-list-active-files").append(o),i.tabThumb=o,this.updateTabDropdownVisibility(),c.get(this.controller+"?action=add&path="+encodeURIComponent(t)),e&&this.focus(t),this.checkDraft(t)&&this.markChanged(t)},focus:function(t,i){void 0===i&&(i=!0),this.highlightEntry(t,i),t!=this.getPath()&&(p.editor.setSession(this.sessions[t]),this.history.push(t),c.get(this.controller,{action:"focused",path:t})),this.check(t),amplify.publish("active.onFocus",t)},highlightEntry:function(t,i){void 0===i&&(i=!0),c("#list-active-files li").removeClass("active"),c("#tab-list-active-files li").removeClass("active"),c("#dropdown-list-active-files li").removeClass("active");var e=this.sessions[t];if(0<c("#dropdown-list-active-files").has(e.tabThumb).length)if(i){var s=e.tabThumb;this.moveDropdownMenuItemToTab(s,!0);var a=c("#tab-list-active-files li:last-child");this.moveTabToDropdownMenu(a)}else this.showTabDropdownMenu();else if(0<this.history.length){var o=this.history[this.history.length-1],n=this.sessions[o];0<c("#dropdown-list-active-files").has(n.tabThumb).length&&this.hideTabDropdownMenu()}e.tabThumb.addClass("active"),e.listThumb.addClass("active")},markChanged:function(t){this.sessions[t].listThumb.addClass("changed"),this.sessions[t].tabThumb.addClass("changed")},save:function(e){amplify.publish("active.onSave",e);var s=this;if(e&&!this.isOpen(e)||!e&&!p.editor.getActive())p.message.error(i18n("No Open Files to save"));else{var a,t=(a=e?this.sessions[e]:p.editor.getActive().getSession()).getValue(),o=(e=a.path,function(t){var i=p.active.sessions[e];void 0!==i&&(i.untainted=n,i.serverMTime=t,i.listThumb&&i.listThumb.removeClass("changed"),i.tabThumb&&i.tabThumb.removeClass("changed")),s.removeDraft(e)}),n=t.slice(0);a.serverMTime&&a.untainted?p.workerManager.addTask({taskType:"diff",id:e,original:a.untainted,changed:n},function(t,i){t?p.filemanager.savePatch(e,i,a.serverMTime,{success:o}):p.filemanager.saveFile(e,n,{success:o})},this):p.filemanager.saveFile(e,n,{success:o})}},saveAll:function(){for(var t in this.sessions)this.sessions[t].listThumb.hasClass("changed")&&p.active.save(t)},remove:function(t){if(this.isOpen(t)){var i=!0;this.sessions[t].listThumb.hasClass("changed")&&(p.modal.load(450,"components/active/dialog.php?action=confirm&path="+encodeURIComponent(t)),i=!1),i&&this.close(t)}},removeAll:function(t){t=t||!1,amplify.publish("active.onRemoveAll");var i=!1,e=new Array;for(var s in this.sessions)e[s]=s,this.sessions[s].listThumb.hasClass("changed")&&(i=!0);if(!i||t){for(var a in e){(s=this.sessions[a]).tabThumb.remove(),this.updateTabDropdownVisibility(),s.listThumb.remove();var o=[];c.each(this.history,function(t){this!=a&&o.push(this)}),this.history=o,delete this.sessions[a],this.removeDraft(a)}p.editor.exterminate(),c("#list-active-files").html(""),c.get(this.controller+"?action=removeall")}else p.modal.load(450,"components/active/dialog.php?action=confirmAll")},close:function(i){amplify.publish("active.onClose",i);var t=this,e=this.sessions[i];e.tabThumb.hasClass("tab-item")?(e.tabThumb.css({"z-index":1}),e.tabThumb.animate({top:c("#editor-top-bar").height()+"px"},300,function(){e.tabThumb.remove(),t.updateTabDropdownVisibility()})):(e.tabThumb.remove(),t.updateTabDropdownVisibility()),e.listThumb.remove();var s=[];c.each(this.history,function(t){this!=i&&s.push(this)}),this.history=s;var a=c('#tab-list-active-files li[data-path!="'+i+'"]');if(0==a.length)p.editor.exterminate();else{var o="";o=0<this.history.length?this.history[this.history.length-1]:c(a[0]).attr("data-path");var n=this.sessions[o];p.editor.removeSession(e,n),this.focus(o)}delete this.sessions[i],c.get(this.controller+"?action=remove&path="+encodeURIComponent(i)),this.removeDraft(i)},rename:function(t,i){var e=function(t,i){var e=this.sessions[t].tabThumb;e.attr("data-path",i);var s=i;p.project.isAbsPath(i)&&(s=i.substring(1)),e.find(".label").text(s),this.sessions[i]=this.sessions[t],this.sessions[i].path=i,delete this.sessions[t];for(var a=0;a<this.history.length;a++)this.history[a]===t&&(this.history[a]=i)};if(this.sessions[t]){e.apply(this,[t,i]);for(var s=0;s<p.editor.instances.length;s++)p.editor.instances[s].getSession().path===i&&p.editor.setActive(p.editor.instances[s]);var a=this.sessions[i],o=p.filemanager.getExtension(i),n=p.editor.selectMode(o),l=function(){p.editor.setModeDisplay(a),a.removeListener("changeMode",l)};a.on("changeMode",l),a.setMode("ace/mode/"+n)}else{var r;for(var h in this.sessions)(r=h.replace(t,i))!==h&&e.apply(this,[h,r])}c.get(this.controller+"?action=rename&old_path="+encodeURIComponent(t)+"&new_path="+encodeURIComponent(i),function(){amplify.publish("active.onRename",{oldPath:t,newPath:i})})},openInBrowser:function(){var t=this.getPath();t?p.filemanager.openInBrowser(t):p.message.error("No Open Files")},getSelectedText:function(){var t=this.getPath(),i=this.sessions[t];if(t&&this.isOpen(t))return i.getTextRange(p.editor.getActive().getSelectionRange());p.message.error(i18n("No Open Files or Selected Text"))},insertText:function(t){p.editor.getActive().insert(t)},gotoLine:function(t){p.editor.getActive().gotoLine(t,0,!0)},move:function(t){if(0!==c("#tab-list-active-files li").length){var i=null,e=null;"up"==t?(0<(e=c("#tab-list-active-files li.active")).length&&0===(i=e.prev("li")).length&&0===(i=c("#dropdown-list-active-files li:last-child")).length&&(i=c("#tab-list-active-files li:last-child")),0<(e=c("#dropdown-list-active-files li.active")).length&&0===(i=e.prev("li")).length&&(i=c("#tab-list-active-files li:last-child"))):(0<(e=c("#tab-list-active-files li.active")).length&&0===(i=e.next("li")).length&&0===(i=c("#dropdown-list-active-files li:first-child")).length&&(i=c("#tab-list-active-files li:first-child")),0<(e=c("#dropdown-list-active-files li.active")).length&&0===(i=e.next("li")).length&&(i=c("#tab-list-active-files li:first-child"))),i&&this.focus(i.attr("data-path"),!1)}},initTabDropdownMenu:function(){var i=this,t=c("#dropdown-list-active-files"),e=c("#tab-dropdown-button"),s=c("#tab-close-button");t.appendTo(c("body")),e.click(function(t){t.stopPropagation(),i.toggleTabDropdownMenu()}),s.click(function(t){t.stopPropagation(),i.removeAll()})},showTabDropdownMenu:function(){c("#dropdown-list-active-files").is(":visible")||this.toggleTabDropdownMenu()},hideTabDropdownMenu:function(){c("#dropdown-list-active-files").is(":visible")&&this.toggleTabDropdownMenu()},toggleTabDropdownMenu:function(){var t=c("#dropdown-list-active-files");if(t.css({top:c("#editor-top-bar").height()+"px",right:"20px",width:"200px"}),t.slideToggle("fast"),t.is(":visible")){var i=function(){t.hide(),c(window).off("click",i)};c(window).on("click",i)}},moveTabToDropdownMenu:function(t,i){void 0===i&&(i=!1),t.remove(),path=t.attr("data-path");var e=this.createMenuItemThumb(path);i?c("#dropdown-list-active-files").prepend(e):c("#dropdown-list-active-files").append(e),t.hasClass("changed")&&e.addClass("changed"),t.hasClass("active")&&e.addClass("active"),this.sessions[path].tabThumb=e},moveDropdownMenuItemToTab:function(t,i){void 0===i&&(i=!1),t.remove(),path=t.attr("data-path");var e=this.createTabThumb(path);i?c("#tab-list-active-files").prepend(e):c("#tab-list-active-files").append(e),t.hasClass("changed")&&e.addClass("changed"),t.hasClass("active")&&e.addClass("active"),this.sessions[path].tabThumb=e},isTabListOverflowed:function(t){void 0===t&&(t=!1);var i=c("#tab-list-active-files li"),e=i.length;if(t&&(e+=1),e<=1)return!1;var s=0;i.each(function(t){s+=c(this).outerWidth(!0)}),t&&(s+=c(i[i.length-1]).outerWidth(!0));var a=c(".sidebar-handle").width();p.sidebars.isLeftSidebarOpen&&(a=c("#sb-left").width());var o=c(".sidebar-handle").width();p.sidebars.isRightSidebarOpen&&(o=c("#sb-right").width());c("#tab-list-active-files").width();var n=c("#tab-dropdown").width(),l=c("#tab-close").width();return window.innerWidth-a-o-n-l-s-30<0},updateTabDropdownVisibility:function(){for(;this.isTabListOverflowed();){var t=c("#tab-list-active-files li:last-child");if(1!=t.length)break;this.moveTabToDropdownMenu(t,!0)}for(;!this.isTabListOverflowed(!0);){var i=c("#dropdown-list-active-files li:first-child");if(1!=i.length)break;this.moveDropdownMenuItemToTab(i)}0<c("#dropdown-list-active-files li").length?c("#tab-dropdown").show():(c("#tab-dropdown").hide(),c("#dropdown-list-active-files").hide()),1<c("#tab-list-active-files li").length?c("#tab-close").show():c("#tab-close").hide()},splitDirectoryAndFileName:function(t){var i=t.lastIndexOf("/");return{fileName:t.substring(i+1),directory:0==t.indexOf("/")?t.substring(1,i+1):t.substring(0,i+1)}},createListThumb:function(t){return c('<li data-path="'+t+'"><a title="'+t+'"><span></span><div>'+t+"</div></a></li>")},createTabThumb:function(t){return split=this.splitDirectoryAndFileName(t),c('<li class="tab-item" data-path="'+t+'"><a class="label" title="'+t+'">'+split.directory+'<span class="file-name">'+split.fileName+'</span></a><a class="close">x</a></li>')},createMenuItemThumb:function(t){return split=this.splitDirectoryAndFileName(t),c('<li data-path="'+t+'"><a title="'+t+'"><span class="label"></span><div class="label">'+split.directory+'<span class="file-name">'+split.fileName+"</span></div></a></li>")}}}(this,jQuery);