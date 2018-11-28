delete from admin_action_log;
delete from admin_session_log;
delete from access_code;
delete from admin_action_log;
delete from admin_session_log;
delete from pay_in_request;
delete from pay_out_request;
delete from balance_log_mod;
delete from balance_log;
delete from chat_user_ignore;
delete from chat_user_contact;
delete from chat_file;
delete from chat_conversation;
delete from chat_user where not user_id in (1,68,77,78);
update chat_message set outgoing_chat_message_id=null;
delete from chat_message;
delete from invitation;
delete from offer_view;
delete from offer_request_modification;
update offer set accepted_offer_request_id=null;
delete from offer_request;
delete from offer_param_value;
delete from offer_interest;
delete from offer_file;
delete from offer_favorite;
delete from offer;
update user set registration_code_id=null;
delete from registration_code;
delete from remote_log;
delete from search_request_interest;
delete from search_request_offer_details_file;
delete from search_request_offer_file;
delete from search_request_offer_param_value;
delete from search_request_param_value;
delete from search_request_offer;
delete from search_request_favorite;
delete from search_request_file;
delete from search_request;
delete from user_activity_log;
delete from user_bank_data where not user_id in (1,68,77,78);
delete from user_delivery_address where not user_id in (1,68,77,78);
delete from user_device;
delete from user_event;
delete from user_feedback;
delete from user_friend;
delete from user_friend_request;
delete from user_interest where not user_id in (1,68,77,78);
delete from user_offer_request_completed_interest;
delete from user_referral;
delete from user_spam_report;
update user set parent_id=null,
`network_size` = '0',
`invitations` = '0',
`network_levels` = '0',
`new_network_members` = '0',
`new_events` = '0',
`rating` = '0',
`spam_reports` = '0',
`feedback_count` = '0',
`closed_deals` = '0',
`stat_offer_year_turnover` = '0.00',
`stat_messages_per_day` = '0.0',
`stat_active_search_requests` = '0',
`stat_offers_view_buy_ratio` = '0',
`deleted_dt` = NULL,
`deleted_backup` = NULL,
`deleted_email` = NULL,
`deleted_first_name` = NULL,
`deleted_last_name` = NULL,
`packet` = 2,
`free_registrations_limit` = NULL,
`payment_complaints` = '0',
`no_membership_payment_notified` = '0',
`stat_new_offers` = '0',
`stat_new_offers_requests` = '0',
`stat_new_search_requests` = '0',
`stat_new_search_requests_offers` = '0';

delete from user where not id in (1,68,77,78);
