<?php
# MantisBT - A PHP based bugtracking system

# MantisBT is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# MantisBT is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with MantisBT.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Avatar class.
 * @copyright Copyright 2014 MantisBT Team - mantisbt-dev@lists.sourceforge.net
 * @link http://www.mantisbt.org
 * @package MantisBT
 */

/**
 * Auth Flags class
 *
 * @package MantisBT
 * @subpackage classes
 */

require_api( 'access_api.php' );
require_api( 'plugin_api.php' );
require_api( 'user_api.php' );

/**
 * A class that that contains authentication flags.
 */
class AuthFlags {
	/**
	 * Core signup functionality is enabled.
	 * @see $signup_access_level
	 * @var bool|null
	 */
	private $signup_enabled = null;

	/**
	 * The access level to assign to users who use core signup functionality.
	 * @see $signup_enabled
	 * @var int|null
	 */
	private $signup_access_level = null;

	/**
	 * Core anonymous login functionality is enabled.
	 * @see $anonymous_account
	 * @var bool|null
	 */
	private $anonymous_enabled = null;

	/**
	 * User account to designate as the anonymous / guest account.
	 * @see $anonymous_enabled
	 * @var string|null
	 */
	private $anonymous_account = null;

	/**
	 * The access level or array of access levels that can leverage MantisBT native passwords.
	 * @var int|array|null
	 */
	private $access_level_set_password = null;

	/**
	 * The message to display indicating that passwords are not managed by MantisBT native passwords.
	 * @var string|null
	 */
	private $password_managed_elsewhere_message = null;

	/**
	 * The access level or array of access levels that can create and use API tokens.
	 * @var int|array|null
	 */
	private $access_level_create_api_tokens = null;

	/**
	 * The access level or array of access levels that can use native MantisBT login.
	 * @var int|array|null
	 */
	private $access_level_can_use_standard_login = null;

	/**
	 * The login page to use instead of the standard MantisBT login page.  This can be
	 * a plugin page.
	 * @see $logout_page
	 * @var string|null
	 */
	private $login_page = null;

	/**
	 * The logout page to use instead of the standard MantisBT logout page.  This can be
	 * a plugin page.
	 * @see $login_page
	 * @see $logout_redirect_page
	 * @var string|null
	 */
	private $logout_page = null;

	/**
	 * The page to redirect to after successful logout.  This can be a plugin page.  Such
	 * page can display content directly to redirect to a MantisBT page to a remote page.
	 * @see $logout_page
	 * @var string|null
	 */
	private $logout_redirect_page = null;

	/**
	 * The login session lifetime in seconds or 0 for browser session.
	 * @var int|null
	 */
	private $session_lifetime = null;

	/**
	 * Indicates whether 'remember me' option is allowed.
	 * @see $perm_session_lifetime
	 * @var bool|null
	 */
	private $perm_session_enabled = null;

	/**
	 * Indicates the lifetime for 'remember me' sessions.  MantisBT default is 1 year.
	 * @see $perm_session_enabled
	 * @var int|null
	 */
	private $perm_session_lifetime = null;

	/**
	 * Indicates if re-authentication for operations like administrative functions and updating
	 * user profile is enabled.
	 * @see $reauthentication_expiry;
	 * @var bool|null
	 */
	private $reauthentication_enabled = null;

	/**
	 * Indicates the expiry time in seconds after which the user should be asked to reauthenticate
	 * for administrative functions and updating user profile.
	 * @see $reauthentication_enabled
	 * @var int|null
	 */
	private $reauthentication_expiry = null;

	/**
	 * AuthFlags constructor.
	 */
	function __construct() {
	}

	function setSignupEnabled( $p_enabled ) {
		$this->signup_enabled = $p_enabled;
	}

	function getSignupEnabled() {
		if( is_null( $this->signup_enabled ) ) {
			return config_get_global( 'allow_signup' );
		}

		return $this->signup_enabled;
	}

	function setSignupAccessLevel( $p_access_level ) {
		$this->signup_access_level = $p_access_level;
	}

	function getSignupAccessLevel() {
		if( is_null( $this->signup_access_level ) ) {
			return config_get( 'default_new_account_access_level' );
		}

		return $this->signup_access_level;
	}

	function setAnonymousEnabled( $p_enabled ) {
		$this->anonymous_enabled = $p_enabled;
	}

	function getAnonymousEnabled() {
		if( is_null( $this->anonymous_enabled ) ) {
			return config_get_global( 'allow_anonymous_login' );
		}

		return $this->anonymous_enabled;
	}

	function setAnonymousAccount( $p_username ) {
		$this->anonymous_account = $p_username;
	}

	function getAnonymousAccount() {
		if( is_null( $this->anonymous_account ) ) {
			return config_get_global( 'anonymous_account' );
		}

		return $this->anonymous_account;
	}

	function setSetPasswordThreshold( $p_threshold ) {
		$this->access_level_set_password = $p_threshold;
	}

	function getSetPasswordThreshold() {
		if( is_null( $this->access_level_set_password ) ) {
			return ANYBODY;
		}

		return $this->access_level_set_password;
	}

	function setPasswordManagedExternallyMessage( $p_message ) {
		$this->password_managed_elsewhere_message = $p_message;
	}

	function getPasswordManagedExternallyMessage() {
		if( empty( $this->password_managed_elsewhere_message ) ) {
			return lang_get( 'password_managed_elsewhere_message' );
		}
	}

	function setCreateApiTokensThreshold( $p_threshold ) {
		$this->access_level_create_api_tokens = $p_threshold;
	}

	function getCreateApiTokensThreshold() {
		if( is_null( $this->access_level_create_api_tokens ) ) {
			return VIEWER;
		}

		return $this->access_level_create_api_tokens;
	}

	function setUserStandardLoginThreshold( $p_threshold ) {
		$this->access_level_can_use_standard_login = $p_threshold;
	}

	function getUseStandardLoginThreshold() {
		if( is_null( $this->access_level_can_use_standard_login ) ) {
			return ANYBODY;
		}

		return $this->access_level_can_use_standard_login;
	}

	function setLoginPage( $p_page ) {
		$this->login_page = $p_page;
	}

	function getLoginPage() {
		if( is_null( $this->login_page ) ) {
			return 'login_page.php';
		}

		return $this->login_page;
	}

	function setLogoutPage( $p_page ) {
		$this->logout_page = $p_page;
	}

	function getLogoutPage() {
		if( is_null( $this->logout_page ) ) {
			return 'logout_page.php';
		}

		return $this->logout_page;
	}

	function setLogoutRedirectPage( $p_page ) {
		$this->logout_redirect_page = $p_page;
	}

	function getLogoutRedirectPage() {
		if( is_null( $this->logout_redirect_page ) ) {
			return config_get( 'logout_redirect_page' );
		}

		return $this->logout_redirect_page;
	}

	function setSessionLifetime( $p_seconds ) {
		$this->session_lifetime = $p_seconds;
	}

	function getSessionLifetime() {
		if( is_null( $this->session_lifetime ) ) {
			return 0;
		}

		return $this->session_lifetime;
	}

	function setPermSessionEnabled( $p_enabled ) {
		$this->perm_session_enabled = $p_enabled;
	}

	function getPermSessionEnabled() {
		if( is_null( $this->perm_session_enabled ) ) {
			return config_get_global( 'allow_permanent_cookie' ) != OFF;
		}

		return $this->perm_session_enabled;
	}

	function setPermSessionLifetime( $p_seconds ) {
		$this->perm_session_lifetime = $p_seconds;
	}

	function getPermSessionLifetime() {
		if( is_null( $this->perm_session_lifetime ) ) {
			return config_get_global( 'cookie_time_length' );
		}

		return $this->perm_session_lifetime;
	}

	function setReauthenticationEnabled( $p_enabled ) {
		$this->reauthentication_enabled = $p_enabled;
	}

	function getReauthenticationEnabled() {
		if( is_null( $this->reauthentication_enabled ) ) {
			return config_get( 'reauthentication' );
		}

		return $this->reauthentication_enabled;
	}

	function setReauthenticationLifetime( $p_seconds ) {
		$this->reauthentication_expiry = $p_seconds;
	}

	function getReauthenticationLifetime() {
		if( is_null( $this->reauthentication_expiry ) ) {
			return config_get( 'reauthentication_expiry' );
		}

		return $this->reauthentication_expiry;
	}
}
