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
import type {EgwFrameworkApp, FilterInfo} from "../../kdots/js/EgwFrameworkApp";
import type {Et2Nextmatch} from "../../api/js/etemplate/Et2Nextmatch/Et2Nextmatch";

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
	 * Current news in the user's language is what the list shows by default, and filter2 only chooses how they are shown
	 *
	 * @param filterValues
	 * @param fwApp
	 */
	getFilterInfo(filterValues : { [id : string] : any }, fwApp : EgwFrameworkApp) : FilterInfo
	{
		const values = {...(filterValues ?? {})};
		values.col_filter = {...(values.col_filter ?? {})};
		delete values.filter2;
		if(values.col_filter.visible == 'now')
		{
			delete values.col_filter.visible;
		}
		if(values.col_filter.news_lang == this.egw.preference('lang', 'common'))
		{
			delete values.col_filter.news_lang;
		}
		return fwApp.filterInfo(values);
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
					const all_users = this.et2.getWidgetById('read_all_users');
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
						const nm = this.et2 ? this.et2.getWidgetById('nm') : null;
						if (nm) nm.applyFilters();
						break;
				}
			}
		}
		//Refresh handler for news_admins integrated in calendar
		if (_app == 'news_admin' && _id && _type !='delete')
		{
			const info_type = egw.dataGetUIDdata(_app+"::"+_id)?egw.dataGetUIDdata(_app+"::"+_id).data.info_type:false;
			const cal_show = egw.preference('cal_show','news_admin')||false;

			if (info_type && cal_show)
			{
				const rex = RegExp(info_type,'gi');
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
		const state = super.getState();

		const nm = this.et2 ? this.et2.getArrayMgr('content').data.nm : {};
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
		const to_set = {col_filter: null, filter: '', filter2: '', cat_id: '', search: '', action: null};
		if(typeof state.state === 'undefined')
		{
			state.state = {};
		}
		for(const name in to_set)
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
		const filter = this.et2.getWidgetById('filter');
		const nm = this.et2.getWidgetById('nm');
		const dates = this.et2.getWidgetById('news_admin.index.dates');
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
	 * Submit one of the category list's "Change" read / write permission dialogs
	 *
	 * The dialogs are real <et2-dialog>s, so Et2NextmatchActionController.openActionPopup() just sets
	 * their .selectedIds and shows them; the window.nm_popup_action/nm_popup_ids globals the legacy
	 * nm_submit_popup() used are never set.  The clicked button lands in the submitted content (eg.
	 * reader_popup[reader_action][add]), which tells news_admin_ui::cats() what to do.
	 *
	 * @param _event
	 * @param _widget the clicked button
	 * @param _action_id the nm action the dialog was opened for, "reader" or "writer"
	 * @return false to stop the button's own submit
	 */
	submit_popup(_event : Event, _widget, _action_id : string) : boolean
	{
		const dialog = <any>_widget.closest('et2-dialog');
		const nm = <Et2Nextmatch>_widget.getInstanceManager()?.widgetContainer?.getWidgetById('nm');
		if(!nm)
		{
			return false;
		}
		// Prefer the live selection - it still carries "select all", which the dialog's
		// .selectedIds (a plain array of ids) does not
		const selection = nm.getSelection();
		if(!selection.all && dialog?.selectedIds?.length)
		{
			selection.ids = dialog.selectedIds;
		}
		nm.executeAction(_action_id, selection, {nmAction: "submit"});
		dialog?.close();
		return false;
	}

	/**
	 * Add email from addressbook
	 *
	 * @param ab_id
	 * @param info_cc
	 */
	add_email_from_ab(ab_id,info_cc)
	{
		const ab = <HTMLSelectElement>document.getElementById(ab_id);

		if (!ab || !ab.value)
		{
			document.querySelectorAll<HTMLElement>("tr.hiddenRow").forEach(row => row.style.display = "table-row");
		}
		else
		{
			const cc = <HTMLInputElement>document.getElementById(info_cc);

			let i;
			for(i=0; i < ab.options.length && ab.options[i].value != ab.value; ++i) ;

			if (i < ab.options.length)
			{
				cc.value += (cc.value?', ':'')+ab.options[i].text.replace(/^.* <(.*)>$/,'$1');
				ab.value = '';
				// call the native change-event handler directly (no synthetic Event needed at runtime)
				(<any>ab).onchange();
				document.querySelectorAll<HTMLElement>("tr.hiddenRow").forEach(row => row.style.display = "none");
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
		const widget = this.et2.getWidgetById('action');
		const template = this.et2.getInstanceManager();
		let id;
		if (template)
		{
			id = template.widgetContainer.getArrayMgr('content').data['info_id'];
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
		const id = _selected[0].id.replace(/^news_admin::/g,'');
		egw.open(id,'news_admin','edit',{print:1});
	}

	/**
	 * Trigger print() onload window
	 */
	news_admin_print_preview_onload()
	{
		const node = document.getElementById('news_admin-edit-print');
		node?.addEventListener('load', () => {
			let isLoadingCompleted = true;
			const onSubtreeModified = () => {
				isLoadingCompleted = false;
				node.removeEventListener("DOMSubtreeModified", onSubtreeModified);
			};
			node.addEventListener("DOMSubtreeModified", onSubtreeModified);
			setTimeout(() => {
				isLoadingCompleted = false;
			}, 1000);
			const interval = setInterval(() => {
				if (!isLoadingCompleted)
				{
					clearInterval(interval);
					this.news_admin_print_preview();
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
		const nm = widget.getRoot().getWidgetById('nm');
		const nm_value = nm.getValue() || {};

		// It's important that all these keys are here, they override the link
		// registry.
		const extras = {
			type: _type || nm_value.filter || "",
			cat_id: nm_value.cat_id || "",
			action: _action || "",
			action_id: _action_id != '0' ? _action_id : "" || ""
		};
		egw.open('','news_admin','add',extras);
	}
}
app.classes.news_admin = NewsAdminApp;