/**
  * @todo:
  * add session timeout detector
  * FILE UPLOADER
  * image uploader (with cropper) - multiple image support & multiple input field support
  * multiple file uploader - easy to add multiple input fields and link to 3rd party table
  * -- resize to be done on front end so is not a necessary feature -- 
  */

var cms = {
	
        oStatusMsg : false,
        formresponse : false,
        formresponseHideDelay : 2000,
        sBasePath : false,
	
        init : function()
        {
                cms.oStatusMsg = base.doc.getElementById('jsStatusMessage');
                this.handleFormMessages();
                var bp = base.doc.getElementById('basepath');
                if(bp) this.sBasePath = bp.innerHTML;
                this.transformWYSIWYGs();
                return false;
        },
	
        gallery : {
                
                oLastQuickEdit : false,
                
                init : function ()
                {
                        cms.jspopup.populate('Add Image', '');
                        this.moveForm();
                        cms.jspopup.toggle('none');
                },
		
                moveForm : function ()
                {
                        $('#jspopup_message').append($('#gallery_whiteboard_fields'));
                },
		
                quickedit : function (inc, obj)
                {
                        var item = obj.parentNode.parentNode;
                        var c = item.childNodes;
                        var l = (c.length - 1);
                        for(var i = l; i >= 0; i--)
                        {
                                if(c[i].className == 'iptwrapper')
                                {
                                        c[i].style.display = 'block';
                                        if(this.oLastQuickEdit) this.oLastQuickEdit.style.display = 'none';
                                        this.oLastQuickEdit = c[i];
                                        break;
                                }
                        }
                        return false;
                },
                
                closeQuickedit : function (obj)
                {
                        if(this.oLastQuickEdit)
                        {
                                this.oLastQuickEdit.style.display = 'none';
                                this.oLastQuickEdit = false;
                        }
                        return false;
                },
                
                refreshThumbs : function ()
                {
                        var turl = './galleries/refresh?bodyonly';
                        $.ajaxQueue({
                                url: turl,
                                type: 'GET',
                                success: function(data)
                                {
                                        $('#gallery_whiteboard').html(data);
                                }
                        });
                        
                        return false;
                },
                
                navPrev : function (id)
                {
                        var turl = './galleries/navprev/' + id + '?bodyonly';
                        $.ajaxQueue({
                                url: turl,
                                type: 'GET',
                                success: function(data)
                                {
                                        cms.gallery.refreshThumbs();
                                }
                        }); 
                        return false;
                },
                
                navNext : function (id)
                {
                        var turl = './galleries/navnext/' + id + '?bodyonly';
                        $.ajaxQueue({
                                url: turl,
                                type: 'GET',
                                success: function(data)
                                {
                                        cms.gallery.refreshThumbs();
                                }
                        }); 
                        return false;
                },
                
                imgonline : function (id)
                {
                        var turl = './galleries/imgoffline/' + id + '?bodyonly';
                        $.ajaxQueue({
                                url: turl,
                                type: 'GET',
                                success: function(data)
                                {
                                        cms.gallery.refreshThumbs();
                                }
                        }); 
                        return false;
                },
                
                imgoffline : function (id)
                {
                        var turl = './galleries/imgonline/' + id + '?bodyonly';
                        $.ajaxQueue({
                                url: turl,
                                type: 'GET',
                                success: function(data)
                                {
                                       cms.gallery.refreshThumbs();
                                }
                        }); 
                        return false;
                }
        },
	
        pageSpecific : { // METHODS BESPOKE TO AN INDIVIDUAL PAGE SECTION
		
                pages : {
			
                        typeChangeInit : false,
			
                        typeChange : function (obj)
                        {
                                base.validationlib.resetValSkipInputs();
                                var val = obj.options[obj.selectedIndex].value;
                                var div_freelink = base.doc.getElementById('cmspageedit_freelink');
                                var div_inputs = base.doc.getElementById('cmspageedit_inputs');
                                if(div_freelink && div_inputs)
                                {
                                        // DEFAULT STATES
                                        div_freelink.style.display = 'none';
                                        div_inputs.style.display = 'block';
                                        if((val == -1) || (val > 0)) 
                                        // LINK TO OTHER WEBSITE || LINK TO INTERNAL PAGE
                                        {
                                                div_inputs.style.display = 'none';
                                                base.validationlib.addValSkipInputs('freelink_txt');
                                                base.validationlib.addValSkipInputs('pagename_txt');
                                                base.validationlib.addValSkipInputs('customurl_txt');
                                                base.validationlib.addValSkipInputs('metatitle_txt');
                                                base.validationlib.addValSkipInputs('metadesc_txt');
                                                if(val == -1) // LINK TO OTHER WEBSITE
                                                {
                                                        div_freelink.style.display = 'block';
                                                }
                                        }
                                }
                                return false;
                        }
			
                },
		
                products : {
			
                        chkListInit : function()
                        {
                                return false; // REMOVE TO ENABLE HIERARCHY TICK MANAGEMENT
                                var container = base.doc.getElementById('category_list');
                                if(container)
                                {
                                        var lis = container.getElementsByTagName('li');
                                        var lilen = lis.length;
                                        for(var i = 0; i < lilen; i++)
                                        {
                                                var inputs = lis[i].getElementsByTagName('input');
                                                if(typeof(inputs[0]) != 'undefined')
                                                {
                                                        inputs[0].onclick = function ()
                                                        {
                                                                var step = this.id.substr(this.id.lastIndexOf('_')+1);
                                                                if(this.checked && (step > 0))
                                                                {
                                                                        // ENSURE ALL PARENT NODES ARE TICKED
                                                                        var elem = this.parentNode.previousSibling;
                                                                        if(elem)
                                                                        {
                                                                                cms.pageSpecific.lib.checkAllCheckboxes(step, elem, 'up');
                                                                        }
                                                                } else {
                                                                        // NOT CHECKED - ENSURE CHILD NODES ARE UNCHECKED TOO
                                                                        var elem = this.parentNode.nextSibling;
                                                                        if(elem)
                                                                        {
                                                                                var ninputs = elem.getElementsByTagName('input');
                                                                                if(ninputs[0])
                                                                                {
                                                                                        var nstep = ninputs[0].id.substr(ninputs[0].id.lastIndexOf('_')+1);
                                                                                        if(nstep > step)
                                                                                        {
                                                                                                cms.pageSpecific.lib.checkAllCheckboxes(step, elem, 'down', 'unchecked');
                                                                                        }
                                                                                }
                                                                        }
                                                                }
                                                        };
                                                }
                                        }
                                }
                        }
			
                },
		
                lib : {
			
                        checkAllCheckboxes : function (clickedStep, oCurrentContainer, sDir, sCheckedType)
                        {
                                if(oCurrentContainer.nodeType == 1)
                                {
                                        if(oCurrentContainer.className.indexOf('item_')!==-1)
                                        {
                                                if(!sCheckedType) sCheckedType = 'checked';
					
                                                var inputs = oCurrentContainer.getElementsByTagName('input');
                                                if(typeof(inputs[0]) != 'undefined')
                                                {
                                                        var nextstep = inputs[0].id.substr(inputs[0].id.lastIndexOf('_')+1);
                                                        var oelem = false;
                                                        if((sDir == 'up') && (nextstep < clickedStep)) // UP
                                                        {
                                                                oelem = oCurrentContainer.previousSibling;
                                                                if(!inputs[0].checked) inputs[0].checked = 'checked';
                                                        } else if (sDir == 'down') { // GREEDY - NO NEED TO CHECK FOR <> STEP
                                                                oelem = oCurrentContainer.nextSibling;
                                                                if(inputs[0].checked) inputs[0].checked = false;
                                                        } else {
                                                                oelem = oCurrentContainer.previousSibling;
                                                                cms.pageSpecific.lib.checkAllCheckboxes(clickedStep, oelem, sDir, sCheckedType);
                                                                return false;
                                                        }
                                                        if(nextstep > 0) cms.pageSpecific.lib.checkAllCheckboxes(clickedStep, oelem, sDir, sCheckedType);
                                                }
                                        }
                                }
                        }			
                }
        },
	
        transformWYSIWYGs : function ()
        {
                var txtareas = base.doc.getElementsByTagName('textarea');
                var len = txtareas.length;
                for(var i = 0; i < len; i++)
                {
                        if(txtareas[i].className.indexOf('wysiwyg')!==-1)
                        {
                                if(!txtareas[i].id || txtareas[i].id == '') txtareas[i].id = 'wysiwyg_dyn_txtarea';
                                var oFCKeditor = new FCKeditor(txtareas[i].id);
                                oFCKeditor.BasePath = this.sBasePath + "/scripts/fckeditor/" ;
                                oFCKeditor.ReplaceTextarea();
                        }
                }
                return false;
        },
	
        addTagcloudContentToInput : function (sTargetId, sInputId)
        {
                var oInput = base.doc.getElementById(sInputId);
                if(oInput)
                {
                        //var html = CKEDITOR.instances[sTargetId].getData();
                        var html = base.doc.getElementById(sTargetId).value;
                        var oWordCounter = this.tagcloud(html);
                        var rethtml = '';
                        for(var i in oWordCounter)
                        {
                                rethtml += (i + ',');
                        }
                        oInput.value = rethtml;
                }
                return false;
        },
	
        tagcloud : function (ihtml, addspanstyling)
        {
                var excludedWords = ['and','the','of','a','be','here', 'heres', 'my','our','all','also','see','at','can','if','but','in','for','i','or','to','from','we','they','have', 'there','theres', 'theirs', 'they\'re', 'their','i\'ll', 'we\'ll', 'can\'t', 'could', 'are', 'on', 'you', 'is', 'this', 'as', 'etc', 'e.t.c', 'it', 'true', 'false', 'by', 'with', 'some'];
                var len = excludedWords.length;
                var oExcludedWords = new Object();
                for (var i = 0; i < len; i++) oExcludedWords[excludedWords[i]] = true;
                html = ihtml.replace(/&nbsp;/ig," ");
                html = html.replace(/(<([^>]+)>)/ig,"");
                html = html.replace(/(&([^;]+);)/ig,"");
                html = html.replace("'",'');
                html = html.replace(/\W+/g,' ');
                var words = html.split(' ');
                var len = words.length;
                var imax = 0;
                var oWordCounter = new Object();
                for(var i = 0; i < len; i++)
                {
                        if(words[i] != '')
                        {
                                var word= words[i].toLowerCase();
                                if(oExcludedWords[word]) continue;
                                if(!oWordCounter[word]) oWordCounter[word] = 1;
                                else oWordCounter[word]++;
                                if(imax < oWordCounter[word]) imax = oWordCounter[word];
                        }
                }
                if(!addspanstyling) return oWordCounter; // QUIT HERE IF NO HTML STYLING IS NEEDED
                var maxfsize = 50;
                var minfsize = 9;
                maxfsize-=minfsize;
                var html = '<div class="tagcloud_words">';
                for(var i in oWordCounter)
                {
                        var pc = (oWordCounter[i] / imax);
                        var fontsize = (maxfsize * pc) + minfsize;
                        html += '<span title="' + oWordCounter[i] + ' words found" style="font-size:' + fontsize + 'px;">' + i + '</span>';
                }
                html += '</div>';
                return html;
        },
	
        viewkeytags : function (sTargetId)
        {
                //var html = CKEDITOR.instances[sTargetId].getData();
                var html = base.doc.getElementById(sTargetId).value;
                this.jspopup.populate('Content Wording \'Tag Cloud\'', this.tagcloud(html, true));
                this.jspopup.toggle();
                return false;
        },
	
        megamenuedit : {
		
                iSelectedId : 0,
		
                getAndDisplayEditContent : function ()
                {
                        var html = '';
			
                        html += 'There was a problem requesting the megamenu edit content. Please try again later.';
			
                        var turl = 'ajax/categorymegamenu.edit.php';
			
                        $.ajaxQueue({
                                url: turl,
                                type: 'GET',
                                success: function(data)
                                {
                                        cms.jspopup.populate('Add Category To Mega Menu', data);
                                        cms.jspopup.toggle();
                                }
                        });
                },
		
                openCreate : function ()
                {
                        cms.jspopup.init();
                        this.getAndDisplayEditContent();
                        return false;
                },
		
                save : function ()
                {
                        var oCatSel = window.document.getElementById('megamenucat_sel');
                        if(oCatSel)
                        {
                                var catid = oCatSel.options[oCatSel.selectedIndex].value;
                                if(catid > 0)
                                {
                                        // VALID CAT ID
                                        this.iSelectedId = catid;
					
                                        // SEND TO STORAGE SCRIPT
                                        var turl = 'ajax/categorymegamenu.save.php?c=' + this.iSelectedId;
                                        $.ajaxQueue({
                                                url: turl,
                                                type: 'GET',
                                                success: function(data)
                                                {
                                                        if(data != '')
                                                        {
                                                                var tbl = window.document.getElementById('categorymegamenu');
                                                                if(tbl)
                                                                {
                                                                        // UPDATE TABLE CONTENT
                                                                        tbl.innerHTML = data;
						    		
                                                                        // CLOSE POPUP
                                                                        cms.jspopup.populate('', '');
                                                                        cms.jspopup.toggle('none');
                                                                } else {
                                                                        cms.jspopup.populate('Error', 'It appears there was an error. Please contact your website provider');
                                                                }
                                                        }
                                                }
                                        });
                                } else {
                                        window.alert('It appears that you have not selected a valid category.')
                                }
                        }
                        return false;
                },
		
                remove : function(id)
                {
                        if(window.confirm('Are you sure you want to remove this item from the megamenu?'))
                        {
                                var turl = 'ajax/categorymegamenu.remove.php?c=' + id;
				
                                $.ajaxQueue({
                                        url: turl,
                                        type: 'GET',
                                        success: function(data)
                                        {
                                                if(data != '')
                                                {
                                                        var tbl = window.document.getElementById('categorymegamenu');
                                                        if(tbl)
                                                        {
                                                                // UPDATE TABLE CONTENT
                                                                tbl.innerHTML = data;
					    		
                                                                // CLOSE POPUP
                                                                cms.jspopup.populate('', '');
                                                                cms.jspopup.toggle('none');
                                                        } else {
                                                                cms.jspopup.populate('Error', 'It appears there was an error. Please contact your website provider');
                                                        }
                                                }
                                        }
                                });
                        }
                        return false;
                },
		
                sort : function ( dir, id )
                {
                        var turl = 'ajax/categorymegamenu.sort.php?dir=' + dir + '&id=' + id;
			
                        $.ajaxQueue({
                                url: turl,
                                type: 'GET',
                                success: function(data)
                                {
                                        if(data != '')
                                        {
                                                var tbl = window.document.getElementById('categorymegamenu');
                                                if(tbl)
                                                {
                                                        // UPDATE TABLE CONTENT
                                                        tbl.innerHTML = data;
				    		
                                                        // CLOSE POPUP
                                                        cms.jspopup.populate('', '');
                                                        cms.jspopup.toggle('none');
                                                } else {
                                                        cms.jspopup.populate('Error', 'It appears there was an error. Please contact your website provider');
                                                }
                                        }
                                }
                        });
                        return false;
                }
        },
	
        jspopup : {
		
                bInit : false,
                oJsPopup : false,
                oTitle : false,
                oMessage : false,
		
                init : function ()
                {
                        if(!this.bInit)
                        {
                                this.oJsPopup = base.doc.getElementById('jspopup');
                                this.oTitle = base.doc.getElementById('jspopup_title');
                                this.oMessage = base.doc.getElementById('jspopup_message');
                        }
                        this.bInit = true;
                },
		
                toggle : function (sVis)
                {
                        if(!this.bInit) this.init();
                        if(this.oJsPopup)
                        {
                                if(!sVis) sVis = 'block';
                                this.oJsPopup.style.display = sVis;
                        }
                        return false;
                },
		
                populate : function (sTitle, sMessage)
                {
                        if(!this.bInit) this.init();
                        this.oTitle.innerHTML = sTitle;
                        this.oMessage.innerHTML = sMessage;
                        return false;
                }
        },
	
        handleFormMessages : function ()
        {
                this.formresponse = base.doc.getElementById('formresponse');
                if(this.formresponse)
                {
                        setTimeout('cms.hideFormMessage();', this.formresponseHideDelay);
                }
                return false;
        },
	
        hideFormMessage : function ()
        {
                $(this.formresponse).fadeOut('slow');
                return false;
        },

        ajaxThis : function(state, aObj, showMessage, followupFuncName)
        {
                var turl = aObj.href;
                turl += '?' + state;
		
                $.ajaxQueue({
                        url: turl,
                        type: 'GET',
                        success: function(data)
                        {
                                if(typeof(cms[followupFuncName]) == 'function')
                                {
                                        cms[followupFuncName](aObj, data);
                                }
                        }
                });
		
                return false;
        },
	
        changeSortOrder : function(obj, html)
        {
                if(html.indexOf('"phpError"')===-1)
                {
                        var msg = 'Sort Order updated successfully';
                        this.showMessage(msg);
                } else {
                        var msg = 'There was a problem updating the Sort Order';
                        this.showError(msg);
                }	
                this.refreshBody(obj, html);
        },
	
        onlineToggle : function(obj)
        {
                var msg = 'Item has been toggled online';
                var shref = obj.href;
                if(shref.indexOf('/offline/')!==-1)
                {
                        shref = shref.replace('/offline/', '/online/');
                        msg = 'Item has been toggled offline';
                } else {
                        shref = shref.replace('/online/', '/offline/');
                }
                obj.href = shref;
                var ihtml = obj.innerHTML;
                if(ihtml.indexOf('offline')!==-1)
                {
                        ihtml = ihtml.replace('offline', 'online');
                } else {
                        ihtml = ihtml.replace('online', 'offline');
                }
                obj.innerHTML = ihtml;
                var cname = obj.className;
                if(cname.indexOf('offline')!==-1)
                {
                        cname = cname.replace('offline', 'online');
                } else {
                        cname = cname.replace('online', 'offline');
                }
                obj.className = cname;
                this.showMessage(msg);
                return false;
        },
	
        deleteItem : function(obj)
        {
                if(window.confirm('Are you sure you wish to delete this item?'))
                {
                        this.ajaxThis('bodyonly', obj, true, 'refreshBody');
                }
                return false;
        },
	
        /**
	  * refreshBody
	  * Used to reload an entire page body in the event of an ajax call
	  * To be used primarily for CMS list sections such as the main 'pages' section
	  * during a sort order change / item being deleted.
	  */
        refreshBody : function(obj, html)
        {
                var contentarea = base.doc.getElementById('content_wrapper');
                if(contentarea)
                {
                        contentarea.innerHTML = html;
                }
                return false;
        },
	
        showMessage : function(msg)
        {
                if(cms.oStatusMsg)
                {
                        cms.oStatusMsg.innerHTML = '<ul id="formresponse" class="formsuccess"><li>' + msg + '</li></ul>';
                        this.handleFormMessages();
                }
                return false;
        },
	
        showError : function(msg)
        {
                if(cms.oStatusMsg)
                {
                        cms.oStatusMsg.innerHTML = '<ul id="formresponse" class="formerrors"><li>' + msg + '</li></ul>';
                        this.handleFormMessages();
                }
                return false;
        },
	
        ajxSearch : {
		
                oSearchContainer : false,
                oSearchResults : false,
                oSearchInput : false,
                oScrollWrapper : false,
                iCurrentTopAdditionalOffset : 0,
                aAddedIds : {},
		
                init : function ()
                {
                        this.oSearchContainer = base.doc.getElementById('search_container');
                        this.oSearchResults = base.doc.getElementById('search_results');
                        this.oScrollWrapper = base.doc.getElementById('search_results_scrollwrapper');
                        if(this.oSearchContainer) this.searchInit();
                        if(this.oSearchContainer) this.loadExistingIds();
                },
		
                loadExistingIds : function ()
                {
                        var c = base.doc.getElementById('search_store');
                        var as = new Object(), asi = 0;
                        if(c)
                        {
                                var uls = c.getElementsByTagName('ul');
                                var ulslen = uls.length;
                                for(var i = 0; i < ulslen; i++)
                                {
                                        as[asi] = new Object();
                                        as[asi]['id'] = '';
                                        as[asi]['desc1'] = '';
                                        as[asi]['part'] = '';
                                        var lis = uls[i].getElementsByTagName('li');
                                        var lilen = lis.length;
                                        for(var x = 0; x < lilen; x++)
                                        {
                                                if(lis[x].className.indexOf('jsv_')!==-1)
                                                {
                                                        var cname = lis[x].className.replace('jsv_', '');
                                                        as[asi][cname] = lis[x].innerHTML;
                                                }
                                        }
                                        asi++;
                                }
                        }
                        for(var i = 0; i < asi; i++)
                        {
                                this.additem(as[i]['id'], as[i]['desc1'], as[i]['part']);
                        }
                },
		
                searchInit : function ()
                {
                        this.oSearchInput = base.doc.getElementById('search_txt');
                        if(this.oSearchInput)
                        {
                                this.oSearchInput.onkeyup = this.oSearchInput.onfocus = cms.ajxSearch.search;
                        }
                },
		
                searchBoxPosition : 'above',
		
                search : function()
                {
                        var v = this.value;
                        if(v == '')
                        {
                                cms.ajxSearch.oSearchResults.style.display = 'none';
                        } else {
                                var h = base.cumulativeOffset(this);
                                var turl = './ajax/ajxsearch.php?qu=' + encodeURIComponent(v);
                                $.ajaxQueue({
                                        url: turl,
                                        type: 'GET',
                                        success: function(data)
                                        {
                                                if(h.top <= 260) // SHOW ABOVE OR BELOW
                                                {
                                                        cms.ajxSearch.searchBoxPosition = 'below';
                                                        cms.ajxSearch.oSearchResults.style.top = (48 + cms.ajxSearch.iCurrentTopAdditionalOffset).toString() + 'px';
                                                        cms.ajxSearch.oSearchResults.style.borderWidth = '0 1px 1px';
                                                } else {
                                                        cms.ajxSearch.searchBoxPosition = 'above';
                                                        cms.ajxSearch.oSearchResults.style.top = '-300px';
                                                        cms.ajxSearch.oSearchResults.style.borderWidth = '1px 1px 0';
                                                }
                                                cms.ajxSearch.oSearchResults.style.display = 'block';
                                                cms.ajxSearch.oSearchResults.innerHTML = data;
                                                cms.ajxSearch.init();
                                        }
                                });
                        }
                },
		
                hidesearchwindow : function()
                {
                        this.oSearchResults.style.display = 'none';
                        return false;
                },
		
                additem : function(id, desc1, part)
                {
                        if(!this.aAddedIds[id])
                        {
                                this.aAddedIds[id] = {};
                                this.aAddedIds[id].id = id;
                                this.aAddedIds[id].desc1 = desc1;
                                this.aAddedIds[id].part = part;
                                this.loadSelectedMemberList();
                                this.tabsHaveChanged();
                        }
			
                        return false;
                },
		
                removemember : function(id)
                {
                        this.aAddedIds[id] = false;
                        this.loadSelectedMemberList();
                        this.tabsHaveChanged();
                        return false;
                },
		
                tabsHaveChanged : function()
                {
                        if(this.searchBoxPosition == 'below')
                        {
                                var defaultTopPos = 48;
                                var t = defaultTopPos;
                                cms.ajxSearch.iCurrentTopAdditionalOffset = base.doc.getElementById('search_store').offsetHeight;
                                t += cms.ajxSearch.iCurrentTopAdditionalOffset;
                                cms.ajxSearch.oSearchResults.style.top = t.toString() + 'px';  
                        }
                        // RE-ASSIGN FOCUS
                        this.oSearchInput.focus();
                },
		
                loadSelectedMemberList : function()
                {
                        var oC = base.doc.getElementById('search_store');
                        oC.innerHTML = '';
                        if(oC)
                        {
                                for(var i in this.aAddedIds)
                                {
                                        if(!this.aAddedIds[i].id) continue;
                                        var dv = base.doc.createElement('div');
                                        var s = base.doc.createElement('span');
                                        var iput = base.doc.createElement('input');
                                        iput.name = 'search_stored_id[]';
                                        iput.type = 'hidden';
                                        iput.value = this.aAddedIds[i].id;
                                        var a = base.doc.createElement('a');
                                        s.innerHTML = this.aAddedIds[i].desc1 + ' (' + this.aAddedIds[i].part + ')';
                                        a.innerHTML = 'Remove';
                                        a.id = 'search_stored_tmp_' + this.aAddedIds[i].id;
                                        a.href = '#';
                                        a.onclick = function()
                                        {
                                                var id = parseInt(this.id.replace('search_stored_tmp_', ''));
                                                cms.ajxSearch.removemember(id);
                                                return false;
                                        };
                                        dv.appendChild(iput);
                                        dv.appendChild(s);
                                        dv.appendChild(a);
                                        oC.appendChild(dv);
                                }
                                var clr = base.doc.createElement('span');
                                clr.innerHTML = '&nbsp;';
                                clr.style.display = 'block';
                                clr.style.clear = 'both';
                                clr.style.height = '1px';
                                oC.appendChild(clr);
                        }
                        return false;
                }
        }
};

$(document).ready(function(){
        cms.init();
        cms.ajxSearch.init();
});