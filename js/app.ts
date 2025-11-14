/**
 * EGroupware - News - Javascript UI
 *
 * @link https://www.egroupware.org
 * @package news_admin
 * @author Nathan Gray
 * @copyright (c) 2014-21 Nathan Gray
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 */

import {EgwApp} from '../../api/js/jsapi/egw_app';
import {nm_open_popup} from "../../api/js/etemplate/et2_extension_nextmatch_actions";
import {Et2Dialog} from "../../api/js/etemplate/Et2Dialog/Et2Dialog";

/**
 * UI for News
 *
 * @augments AppJS
 */
class NewsAdminApp extends EgwApp
{
	/**
	 * Constructor
	 *
	 */
	constructor()
	{
		// call parent
		super('news_admin');
	}

	/**
	 * This function is called when the etemplate2 object is loaded
	 * and ready.  If you must store a reference to the et2 object,
	 * make sure to clean it up in destroy().
	 *
	 * @param {etemplate2} _et2 newly ready object
	 * @param {string} _name template name
	 */
	et2_ready(_et2, _name)
	{
		// call parent
		super.et2_ready(_et2,_name);

		switch(_name)
		{
			case 'news_admin.cat':
				if(this.et2.getArrayMgr('content').getEntry('read_all_users'))
				{
					// Start read permissions hidden if all users is flagged
					var all_users = this.et2.getWidgetById('read_all_users');
					if(all_users.get_value())
					{
						//all_users.change();
					}
				}
		}
	}

	/**
	 * Observer method receives update notifications from all applications
	 *
	 * InfoLog currently reacts to timesheet updates, as it might show time-sums.
	 * @todo only trigger update, if times are shown
	 *
	 * @param {string} _msg message (already translated) to show, eg. 'Entry deleted'
	 * @param {string} _app application name
	 * @param {(string|number)} _id id of entry to refresh or null
	 * @param {string} _type either 'update', 'edit', 'delete', 'add' or null
	 * - update: request just modified data from given rows.  Sorting is not considered,
	 *		so if the sort field is changed, the row will not be moved.
	 * - edit: rows changed, but sorting may be affected.  Requires full reload.
	 * - delete: just delete the given rows clientside (no server interaction neccessary)
	 * - add: requires full reload for proper sorting
	 * @param {string} _msg_type 'error', 'warning' or 'success' (default)
	 * @param {object|null} _links app => array of ids of linked entries
	 * or null, if not triggered on server-side, which adds that info
	 */
	observer(_msg, _app, _id, _type, _msg_type, _links)
	{
		if (typeof _links != 'undefined')
		{
			if (typeof _links.news_admin != 'undefined')
			{
				switch (_app)
				{
					case 'timesheet':
						var nm = this.et2 ? this.et2.getWidgetById('nm') : null;
						if (nm) nm.applyFilters();
						break;
				}
			}
		}
		//Refresh handler for news_admins integrated in calendar
		if (_app == 'news_admin' && _id && _type !='delete')
		{
			var info_type = egw.dataGetUIDdata(_app+"::"+_id)?egw.dataGetUIDdata(_app+"::"+_id).data.info_type:false;
			var cal_show = egw.preference('cal_show','news_admin')||false;

			if (info_type && cal_show)
			{
				var rex = RegExp(info_type,'gi');
				if (cal_show.match(rex))
				{
					//Trigger refresh the whole calendar if the changed news_admin entry is integrated one
					if (typeof app['calendar'] != 'undefined') app.calendar.egw.window.location.reload();
				}
			}
		}
	}

	/**
	 * Retrieve the current state of the application for future restoration
	 *
	 * Reimplemented to add action/action_id from content set by server
	 * when eg. viewing news_admins linked to contacts.
	 *
	 * @return {object} Application specific map representing the current state
	 */
	getState()
	{
		// call parent
		var state = super.getState();

		var nm = this.et2 ? this.et2.getArrayMgr('content').data.nm : {};
		state.action = nm.action || null;
		state.action_id = nm.action_id || null;

		return state;
	}

	/**
	 * Set the application's state to the given state.
	 *
	 * Reimplemented to also reset action/action_id.
	 *
	 * @param {{name: string, state: object}|string} state Object (or JSON string) for a state.
	 *	Only state is required, and its contents are application specific.
	 *
	 * @return {boolean} false - Returns false to stop event propagation
	 */
	setState(state)
	{
		// as we have to set state.state.action, we have to set all other
		// for "No filter" favorite to work as expected
		var to_set = {col_filter: null, filter: '', filter2: '', cat_id: '', search: '', action: null};
		if(typeof state.state === 'undefined')
		{
			state.state = {};
		}
		for(var name in to_set)
		{
			if (typeof state.state[name] == 'undefined') state.state[name] = to_set[name];
		}
		return super.setState(state);
	}

	/**
	 * Enable or disable the date filter
	 *
	 * If the filter is set to something that needs dates, we enable the
	 * header_left template.  Otherwise, it is disabled.
	 */
	filter_change()
	{
		var filter = this.et2.getWidgetById('filter');
		var nm = this.et2.getWidgetById('nm');
		var dates = this.et2.getWidgetById('news_admin.index.dates');
		if(nm && filter)
		{
			switch(filter.getValue())
			{
				case 'bydate':
				case 'duedate':

					if (filter && dates)
					{
						dates.set_disabled(false);
					}
					break;
				default:
					if (dates)
					{
						dates.set_disabled(true);
					}
					break;
			}
		}
	}

	/**
	 * show or hide the details of rows by selecting the filter2 option
	 * either 'all' for details or 'no_description' for no details
	 *
	 * @param {Event} event Change event
	 * @param {et2_nextmatch} nm The nextmatch widget that owns the filter
	 */
	filter2_change(event, nm)
	{
		var filter2 = nm.getWidgetById('filter2');

		if (nm && filter2)
		{
			// Show / hide descriptions
			this.show_details(filter2.value == 'all', nm.getDOMNode(nm));

			// Store selection as implicit preference
			egw.set_preference('news_admin', nm.options.settings.columnselection_pref.replace('-details','')+'-details-pref', filter2.value);

			// Change preference location - widget is nextmatch
			nm.options.settings.columnselection_pref = nm.options.settings.columnselection_pref.replace('-details','') + (filter2.value == 'all' ? '-details' :'');

			// Load new preferences
			var colData = nm.columns.slice();
			for(var i = 0; i < nm.columns.length; i++) colData[i].disabled=false;
			nm._applyUserPreferences(nm.columns, colData);

			// Now apply them to columns
			for(var i = 0; i < colData.length; i++)
			{
				nm.dataview.getColumnMgr().columns[i].set_width(colData[i].width);
				nm.dataview.getColumnMgr().columns[i].set_visibility(!colData[i].disabled);
			}
			nm.dataview.getColumnMgr().updated = true;
			// Update page
			nm.dataview.updateColumns();
		}
	}

	/**
	 * Show or hide details by changing the CSS class
	 *
	 * @param {boolean} show
	 * @param {DOMNode} dom_node
	 */
	show_details(show, dom_node)
	{
		// Show / hide descriptions
        egw.css((dom_node && dom_node.id ? "#"+dom_node.id+' ' : '') + ".et2_box.infoDes","display:" + (show ? "block;" : "none;"));
	}

	confirm_delete_2(_action, _senders)
	{
		var children = false;
		var child_button = jQuery('#delete_sub').get(0) || jQuery('[id*="delete_sub"]').get(0);
		if(child_button)
		{
			for(var i = 0; i < _senders.length; i++)
			{
				if (jQuery(_senders[i].iface.node).hasClass('news_admin_rowHasSubs'))
				{
					children = true;
					break;
				}
			}
			child_button.style.display = children ? 'block' : 'none';
		}
		var callbackDeleteDialog = function (button_id)
		{
			if(button_id == Et2Dialog.YES_BUTTON)
			{

			}
		};
		Et2Dialog.show_dialog(callbackDeleteDialog, this.egw.lang("Do you really want to DELETE this Rule"), this.egw.lang("Delete"), {}, Et2Dialog.BUTTONS_YES_NO_CANCEL, Et2Dialog.WARNING_MESSAGE);
	}

	/**
	 * Confirm delete
	 * If entry has children, asks if you want to delete children too
	 *
	 *@param _action
	 *@param _senders
	 */
	confirm_delete(_action, _senders)
	{
		var children = false;
		var child_button = jQuery('#delete_sub').get(0) || jQuery('[id*="delete_sub"]').get(0);
		if(child_button)
		{
			for(var i = 0; i < _senders.length; i++)
			{
				if (jQuery(_senders[i].iface.getDOMNode()).hasClass('news_admin_rowHasSubs'))
				{
					children = true;
					break;
				}
			}
			child_button.style.display = children ? 'block' : 'none';
		}
		nm_open_popup(_action, _senders);
	}

	/**
	 * Add email from addressbook
	 *
	 * @param ab_id
	 * @param info_cc
	 */
	add_email_from_ab(ab_id,info_cc)
	{
		var ab = document.getElementById(ab_id);

		if (!ab || !ab.value)
		{
			jQuery("tr.hiddenRow").css("display", "table-row");
		}
		else
		{
			var cc = document.getElementById(info_cc);

			for(var i=0; i < ab.options.length && ab.options[i].value != ab.value; ++i) ;

			if (i < ab.options.length)
			{
				cc.value += (cc.value?', ':'')+ab.options[i].text.replace(/^.* <(.*)>$/,'$1');
				ab.value = '';
				ab.onchange();
				jQuery("tr.hiddenRow").css("display", "none");
			}
		}
		return false;
	}

	/**
	 * handle "print" action from "Actions" selectbox in edit news_admin window.
	 * check if the template is dirty then submit the template otherwise just open new window as print.
	 *
	 */
	edit_actions()
	{
		var widget = this.et2.getWidgetById('action');
		var template = this.et2._inst;
		if (template)
		{
			var id = template.widgetContainer.getArrayMgr('content').data['info_id'];
		}
		if (widget)
		{
			switch (widget.get_value())
			{
				case 'print':
					if (template.isDirty())
					{
						template.submit();
					}
					egw.open(id,'news_admin','edit',{print:1});
					break;
				default:
					template.submit();
			}
		}
	}

	/**
	 * Open news_admin entry for printing
	 *
	 * @param {aciton object} _action
	 * @param {object} _selected
	 */
	news_admin_menu_print(_action, _selected)
	{
		var id = _selected[0].id.replace(/^news_admin::/g,'');
		egw.open(id,'news_admin','edit',{print:1});
	}

	/**
	 * Trigger print() onload window
	 */
	news_admin_print_preview_onload()
	{
		var that = this;
		jQuery('#news_admin-edit-print').bind('load',function(){
			var isLoadingCompleted = true;
			jQuery('#news_admin-edit-print').bind("DOMSubtreeModified",function(event){
					isLoadingCompleted = false;
					jQuery('#news_admin-edit-print').unbind("DOMSubtreeModified");
			});
			setTimeout(function() {
				isLoadingCompleted = false;
			}, 1000);
			var interval = setInterval(function(){
				if (!isLoadingCompleted)
				{
					clearInterval(interval);
					that.news_admin_print_preview();
				}
			}, 100);
		});
	}

	/**
	 * Trigger print() function to print the current window
	 */
	news_admin_print_preview()
	{
		this.egw.message('Printing...');
		this.egw.window.print();
	}

	/**
	 *
	 */
	add_link_sidemenu()
	{
		egw.open('','news_admin','add');
	}

	/**
	 * Opens a new edit dialog with some extra url parameters pulled from
	 * standard locations.  Done with a function instead of hardcoding so
	 * the values can be updated if user changes them in UI.
	 *
	 * @param {et2_widget} widget Originating/calling widget
	 * @param _type string Type of news_admin entry
	 * @param _action string Special action for new news_admin entry
	 * @param _action_id string ID for special action
	 */
	add_with_extras(widget,_type, _action, _action_id)
	{
		// We use widget.getRoot() instead of this.et2 for the case when the
		// addressbook tab is viewing a contact + news_admin list, there's 2 news_admin
		// etemplates
		var nm = widget.getRoot().getWidgetById('nm');
		var nm_value = nm.getValue() || {};

		// It's important that all these keys are here, they override the link
		// registry.
		var extras = {
			type: _type || nm_value.filter || "",
			cat_id: nm_value.cat_id || "",
			action: _action || "",
			action_id: _action_id != '0' ? _action_id : "" || ""
		};
		egw.open('','news_admin','add',extras);
	}
}
app.classes.news_admin = NewsAdminApp;