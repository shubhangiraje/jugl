<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property integer $facebook_id
 * @property integer $parent_id
 * @property integer $registration_code_id
 * @property string $registration_dt
 * @property string $registration_ip
 * @property integer $country_id
 * @property string $sex
 * @property string $email
 * @property string $password
 * @property integer $avatar_file_id
 * @property string $first_name
 * @property string $last_name
 * @property string $nick_name
 * @property integer $is_company_name
 * @property string $company_name
 * @property string $status
 * @property string $vip_active_till
 * @property integer $vip_lifetime
 * @property string $next_vip_notification_at
 * @property string $birthday
 * @property string $visibility_birthday
 * @property string $balance
 * @property string $balance_buyed
 * @property string $balance_earned
 * @property integer $failed_logins
 * @property string $phone
 * @property string $street
 * @property string $house_number
 * @property string $visibility_address1
 * @property string $zip
 * @property string $city
 * @property string $visibility_address2
 * @property string $profession
 * @property string $visibility_profession
 * @property string $marital_status
 * @property string $visibility_marital_status
 * @property string $about
 * @property string $visibility_about
 * @property string $access_token
 * @property string $auth_key
 * @property string $validation_status
 * @property string $validation_type
 * @property string $validation_failure_reason
 * @property string $validation_details
 * @property string $validation_changelog
 * @property integer $validation_photo1_file_id
 * @property integer $validation_photo2_file_id
 * @property integer $validation_photo3_file_id
 * @property string $paypal_email
 * @property integer $network_size
 * @property integer $invitations
 * @property integer $network_levels
 * @property integer $new_network_members
 * @property integer $new_events
 * @property integer $rating
 * @property integer $team_rating
 * @property integer $spam_reports
 * @property integer $show_in_become_member
 * @property integer $registered_by_become_member
 * @property integer $feedback_count
 * @property integer $team_feedback_count
 * @property integer $closed_deals
 * @property integer $sms_limit
 * @property integer $sms_sent
 * @property string $stat_offer_year_turnover
 * @property double $stat_messages_per_day
 * @property integer $stat_active_search_requests
 * @property double $stat_offers_view_buy_ratio
 * @property integer $stat_invitations_email
 * @property integer $stat_invitations_sms
 * @property integer $stat_invitations_whatsapp
 * @property integer $stat_invitations_social
 * @property integer $stat_referrals_standart
 * @property integer $stat_referrals_vip
 * @property string $deleted_dt
 * @property string $deleted_backup
 * @property string $deleted_email
 * @property string $deleted_first_name
 * @property string $deleted_last_name
 * @property string $packet
 * @property integer $free_registrations_limit
 * @property integer $payment_complaints
 * @property integer $no_membership_payment_notified
 * @property integer $stat_new_offers
 * @property integer $stat_new_offers_requests
 * @property integer $stat_new_search_requests
 * @property integer $stat_new_search_requests_offers
 * @property integer $stat_awaiting_feedbacks
 * @property string $next_invitation_notification_email
 * @property string $next_invitation_notification_push
 * @property string $invitation_notification_start
 * @property integer $app_login_notifications_sent
 * @property string $stat_buyed_jugl
 * @property string $dt_packet_upgrade
 * @property string $dt_packet_select
 * @property string $parent_registration_bonus
 * @property integer $is_user_profile_delete
 * @property string $dt_status_change
 * @property string $dt_status_active
 * @property integer $setting_off_send_email
 * @property integer $registration_from_desktop
 * @property integer $show_start_popup
 * @property integer $show_friends_invite_popup
 * @property integer $is_moderator
 * @property integer $block_parent_team_requests
 * @property string $dt_parent_change
 * @property integer $teamleader_feedback_notified
 * @property string $company_manager
 * @property string $impressum
 * @property string $agb
 * @property string $validation_phone_status
 * @property string $validation_phone
 * @property string $validation_code
 * @property integer $publish_offer_wo_validation
 * @property integer $publish_search_request_wo_validation
 * @property string $teamleader_feedback_notification_at
 * @property string $ad_status_auto
 * @property integer $delay_invited_member
 *
 * @property BalanceLog[] $balanceLogs
 * @property BalanceLog[] $balanceLogs0
 * @property ChatUserIgnore[] $chatUserIgnores
 * @property GroupChatModeratorLastVisit[] $groupChatModeratorLastVisits
 * @property ChatUser[] $groupChats
 * @property InfoComment[] $infoComments
 * @property InfoCommentVote[] $infoCommentVotes
 * @property InfoComment[] $infoComments0
 * @property Invitation[] $invitations0
 * @property Invitation[] $invitations1
 * @property Invitation[] $invitations2
 * @property KnownDevice[] $knownDevices
 * @property Offer[] $offers
 * @property OfferFavorite[] $offerFavorites
 * @property Offer[] $offers0
 * @property OfferRequest[] $offerRequests
 * @property OfferView[] $offerViews
 * @property Offer[] $offers1
 * @property OfferViewLog[] $offerViewLogs
 * @property PayInRequest[] $payInRequests
 * @property PayOutRequest[] $payOutRequests
 * @property RegistrationCode[] $registrationCodes
 * @property RegistrationCode[] $registrationCodes0
 * @property RegistrationHelpRequest[] $registrationHelpRequests
 * @property SearchRequest[] $searchRequests
 * @property SearchRequestComment[] $searchRequestComments
 * @property SearchRequestFavorite[] $searchRequestFavorites
 * @property SearchRequest[] $searchRequests0
 * @property SearchRequestOffer[] $searchRequestOffers
 * @property TrollboxMessage[] $trollboxMessages
 * @property TrollboxMessage[] $trollboxMessages0
 * @property TrollboxMessageStatusHistory[] $trollboxMessageStatusHistories
 * @property TrollboxMessageVote[] $trollboxMessageVotes
 * @property TrollboxMessage[] $trollboxMessages1
 * @property User $parent
 * @property User[] $users
 * @property File $validationPhoto3File
 * @property File $avatarFile
 * @property RegistrationCode $registrationCode
 * @property File $validationPhoto1File
 * @property File $validationPhoto2File
 * @property Country $country
 * @property UserActivityLog[] $userActivityLogs
 * @property UserBankData[] $userBankDatas
 * @property UserBecomeMemberInvitation[] $userBecomeMemberInvitations
 * @property UserBecomeMemberInvitation[] $userBecomeMemberInvitations0
 * @property User[] $secondUsers
 * @property User[] $users0
 * @property UserDeliveryAddress[] $userDeliveryAddresses
 * @property UserDevice[] $userDevices
 * @property UserEvent[] $userEvents
 * @property UserEvent[] $userEvents0
 * @property UserFeedback[] $userFeedbacks
 * @property UserFeedback[] $userFeedbacks0
 * @property UserFriend[] $userFriends
 * @property UserFriend[] $userFriends0
 * @property User[] $friendUsers
 * @property User[] $users1
 * @property UserFriendRequest[] $userFriendRequests
 * @property UserFriendRequest[] $userFriendRequests0
 * @property UserInfoView $userInfoView
 * @property UserInterest[] $userInterests
 * @property UserModifyLog[] $userModifyLogs
 * @property UserOfferRequestCompletedInterest[] $userOfferRequestCompletedInterests
 * @property Interest[] $interests
 * @property UserPhoto[] $userPhotos
 * @property File[] $files
 * @property UserReferral[] $userReferrals
 * @property UserReferral[] $userReferrals0
 * @property UserSpamReport[] $userSpamReports
 * @property UserSpamReport[] $userSpamReports0
 * @property UserTeamFeedback[] $userTeamFeedbacks
 * @property UserTeamFeedback[] $userTeamFeedbacks0
 * @property UserTeamRequest[] $userTeamRequests
 * @property UserTeamRequest[] $userTeamRequests0 
 */
class User extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'registration_code_id', 'country_id', 'avatar_file_id', 'is_company_name', 'vip_lifetime', 'failed_logins', 'validation_photo1_file_id', 'validation_photo2_file_id', 'validation_photo3_file_id', 'network_size', 'invitations', 'network_levels', 'new_network_members', 'new_events', 'rating', 'team_rating', 'spam_reports', 'show_in_become_member', 'registered_by_become_member', 'feedback_count', 'team_feedback_count', 'closed_deals', 'sms_limit', 'sms_sent', 'stat_active_search_requests', 'stat_invitations_email', 'stat_invitations_sms', 'stat_invitations_whatsapp', 'stat_invitations_social', 'stat_referrals_standart', 'stat_referrals_vip', 'free_registrations_limit', 'payment_complaints', 'no_membership_payment_notified', 'stat_new_offers', 'stat_new_offers_requests', 'stat_new_search_requests', 'stat_new_search_requests_offers', 'stat_awaiting_feedbacks', 'app_login_notifications_sent', 'is_user_profile_delete', 'setting_off_send_email', 'registration_from_desktop', 'show_start_popup', 'show_friends_invite_popup', 'is_moderator', 'block_parent_team_requests', 'teamleader_feedback_notified', 'publish_offer_wo_validation', 'publish_search_request_wo_validation'], 'integer'],
            [['registration_dt', 'vip_active_till', 'next_vip_notification_at', 'birthday', 'deleted_dt', 'next_invitation_notification_email', 'next_invitation_notification_push', 'invitation_notification_start', 'dt_packet_upgrade', 'dt_packet_select', 'dt_status_change', 'dt_status_active', 'dt_parent_change', 'teamleader_feedback_notification_at'], 'safe'],
            [['sex', 'status', 'visibility_birthday', 'visibility_address1', 'visibility_address2', 'visibility_profession', 'marital_status', 'visibility_marital_status', 'visibility_about', 'validation_status', 'validation_type', 'validation_failure_reason', 'validation_details', 'validation_changelog', 'deleted_backup', 'packet', 'impressum', 'agb', 'validation_phone_status', 'ad_status_auto'], 'string'],
            [['password', 'access_token', 'auth_key'], 'required'],
            [['balance', 'balance_buyed', 'balance_earned', 'stat_offer_year_turnover', 'stat_messages_per_day', 'stat_offers_view_buy_ratio', 'stat_buyed_jugl', 'parent_registration_bonus'], 'number'],
            [['registration_ip', 'password', 'first_name', 'last_name', 'company_name', 'phone', 'street', 'house_number', 'zip', 'city', 'profession', 'deleted_first_name', 'deleted_last_name', 'company_manager', 'validation_phone'], 'string', 'max' => 64],
            [['email', 'paypal_email', 'deleted_email'], 'string', 'max' => 128],
            [['nick_name', 'access_token', 'auth_key'], 'string', 'max' => 32],
            [['about'], 'string', 'max' => 16384],
            [['validation_code'], 'string', 'max' => 8],
            [['nick_name'], 'unique'],
            [['email'], 'unique'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['parent_id' => 'id']],
            [['validation_photo3_file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::className(), 'targetAttribute' => ['validation_photo3_file_id' => 'id']],
            [['avatar_file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::className(), 'targetAttribute' => ['avatar_file_id' => 'id']],
            [['registration_code_id'], 'exist', 'skipOnError' => true, 'targetClass' => RegistrationCode::className(), 'targetAttribute' => ['registration_code_id' => 'id']],
            [['validation_photo1_file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::className(), 'targetAttribute' => ['validation_photo1_file_id' => 'id']],
            [['validation_photo2_file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::className(), 'targetAttribute' => ['validation_photo2_file_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id']]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBalanceLogs()
    {
        return $this->hasMany('\app\models\BalanceLog', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBalanceLogs0()
    {
        return $this->hasMany('\app\models\BalanceLog', ['initiator_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChatUserIgnores()
    {
        return $this->hasMany('\app\models\ChatUserIgnore', ['moderator_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupChatModeratorLastVisits()
    {
        return $this->hasMany('\app\models\GroupChatModeratorLastVisit', ['moderator_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupChats()
    {
        return $this->hasMany('\app\models\ChatUser', ['user_id' => 'group_chat_id'])->viaTable('group_chat_moderator_last_visit', ['moderator_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInfoComments()
    {
        return $this->hasMany('\app\models\InfoComment', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInfoCommentVotes()
    {
        return $this->hasMany('\app\models\InfoCommentVote', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInfoComments0()
    {
        return $this->hasMany('\app\models\InfoComment', ['id' => 'info_comment_id'])->viaTable('info_comment_vote', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvitations0()
    {
        return $this->hasMany('\app\models\Invitation', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvitations1()
    {
        return $this->hasMany('\app\models\Invitation', ['referral_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvitations2()
    {
        return $this->hasMany('\app\models\Invitation', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKnownDevices()
    {
        return $this->hasMany('\app\models\KnownDevice', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffers()
    {
        return $this->hasMany('\app\models\Offer', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferFavorites()
    {
        return $this->hasMany('\app\models\OfferFavorite', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffers0()
    {
        return $this->hasMany('\app\models\Offer', ['id' => 'offer_id'])->viaTable('offer_favorite', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferRequests()
    {
        return $this->hasMany('\app\models\OfferRequest', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferViews()
    {
        return $this->hasMany('\app\models\OfferView', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffers1()
    {
        return $this->hasMany('\app\models\Offer', ['id' => 'offer_id'])->viaTable('offer_view', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferViewLogs()
    {
        return $this->hasMany('\app\models\OfferViewLog', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayInRequests()
    {
        return $this->hasMany('\app\models\PayInRequest', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayOutRequests()
    {
        return $this->hasMany('\app\models\PayOutRequest', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegistrationCodes()
    {
        return $this->hasMany('\app\models\RegistrationCode', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegistrationCodes0()
    {
        return $this->hasMany('\app\models\RegistrationCode', ['referral_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegistrationHelpRequests()
    {
        return $this->hasMany('\app\models\RegistrationHelpRequest', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequests()
    {
        return $this->hasMany('\app\models\SearchRequest', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequestComments()
    {
        return $this->hasMany('\app\models\SearchRequestComment', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequestFavorites()
    {
        return $this->hasMany('\app\models\SearchRequestFavorite', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequests0()
    {
        return $this->hasMany('\app\models\SearchRequest', ['id' => 'search_request_id'])->viaTable('search_request_favorite', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequestOffers()
    {
        return $this->hasMany('\app\models\SearchRequestOffer', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrollboxMessages()
    {
        return $this->hasMany('\app\models\TrollboxMessage', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrollboxMessages0()
    {
        return $this->hasMany('\app\models\TrollboxMessage', ['status_changed_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrollboxMessageStatusHistories()
    {
        return $this->hasMany('\app\models\TrollboxMessageStatusHistory', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrollboxMessageVotes()
    {
        return $this->hasMany('\app\models\TrollboxMessageVote', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrollboxMessages1()
    {
        return $this->hasMany('\app\models\TrollboxMessage', ['id' => 'trollbox_message_id'])->viaTable('trollbox_message_vote', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne('\app\models\User', ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany('\app\models\User', ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getValidationPhoto3File()
    {
        return $this->hasOne('\app\models\File', ['id' => 'validation_photo3_file_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAvatarFile()
    {
        return $this->hasOne('\app\models\File', ['id' => 'avatar_file_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegistrationCode()
    {
        return $this->hasOne('\app\models\RegistrationCode', ['id' => 'registration_code_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getValidationPhoto1File()
    {
        return $this->hasOne('\app\models\File', ['id' => 'validation_photo1_file_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getValidationPhoto2File()
    {
        return $this->hasOne('\app\models\File', ['id' => 'validation_photo2_file_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne('\app\models\Country', ['id' => 'country_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserActivityLogs()
    {
        return $this->hasMany('\app\models\UserActivityLog', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserBankDatas()
    {
        return $this->hasMany('\app\models\UserBankData', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserBecomeMemberInvitations()
    {
        return $this->hasMany('\app\models\UserBecomeMemberInvitation', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserBecomeMemberInvitations0()
    {
        return $this->hasMany('\app\models\UserBecomeMemberInvitation', ['second_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSecondUsers()
    {
        return $this->hasMany('\app\models\User', ['id' => 'second_user_id'])->viaTable('user_become_member_invitation', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers0()
    {
        return $this->hasMany('\app\models\User', ['id' => 'user_id'])->viaTable('user_become_member_invitation', ['second_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserDeliveryAddresses()
    {
        return $this->hasMany('\app\models\UserDeliveryAddress', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserDevices()
    {
        return $this->hasMany('\app\models\UserDevice', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserEvents()
    {
        return $this->hasMany('\app\models\UserEvent', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserEvents0()
    {
        return $this->hasMany('\app\models\UserEvent', ['second_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserFeedbacks()
    {
        return $this->hasMany('\app\models\UserFeedback', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserFeedbacks0()
    {
        return $this->hasMany('\app\models\UserFeedback', ['second_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserFriends()
    {
        return $this->hasMany('\app\models\UserFriend', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserFriends0()
    {
        return $this->hasMany('\app\models\UserFriend', ['friend_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFriendUsers()
    {
        return $this->hasMany('\app\models\User', ['id' => 'friend_user_id'])->viaTable('user_friend', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers1()
    {
        return $this->hasMany('\app\models\User', ['id' => 'user_id'])->viaTable('user_friend', ['friend_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserFriendRequests()
    {
        return $this->hasMany('\app\models\UserFriendRequest', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserFriendRequests0()
    {
        return $this->hasMany('\app\models\UserFriendRequest', ['friend_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserInfoView()
    {
        return $this->hasOne('\app\models\UserInfoView', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserInterests()
    {
        return $this->hasMany('\app\models\UserInterest', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserModifyLogs()
    {
        return $this->hasMany('\app\models\UserModifyLog', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserOfferRequestCompletedInterests()
    {
        return $this->hasMany('\app\models\UserOfferRequestCompletedInterest', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInterests()
    {
        return $this->hasMany('\app\models\Interest', ['id' => 'interest_id'])->viaTable('user_offer_request_completed_interest', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserPhotos()
    {
        return $this->hasMany('\app\models\UserPhoto', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany('\app\models\File', ['id' => 'file_id'])->viaTable('user_photo', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserReferrals()
    {
        return $this->hasMany('\app\models\UserReferral', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserReferrals0()
    {
        return $this->hasMany('\app\models\UserReferral', ['referral_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserSpamReports()
    {
        return $this->hasMany('\app\models\UserSpamReport', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserSpamReports0()
    {
        return $this->hasMany('\app\models\UserSpamReport', ['second_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserTeamFeedbacks()
    {
        return $this->hasMany('\app\models\UserTeamFeedback', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserTeamFeedbacks0()
    {
        return $this->hasMany('\app\models\UserTeamFeedback', ['second_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserTeamRequests()
    {
        return $this->hasMany('\app\models\UserTeamRequest', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserTeamRequests0()
    {
        return $this->hasMany('\app\models\UserTeamRequest', ['second_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollowerUsers()
    {
        return $this->hasMany('\app\models\User', ['id' => 'follower_user_id'])->viaTable('user_follower', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollowingUsers()
    {
        return $this->hasMany('\app\models\User', ['id' => 'user_id'])->viaTable('user_follower', ['follower_user_id' => 'id']);
    }


}
