<?php
# TODO: Do autoload for commands.
require_once( dirname( __FILE__ ) . '/Command.php' );

require_api( 'authentication_api.php' );
require_api( 'bug_api.php' );
require_api( 'constant_inc.php' );
require_api( 'config_api.php' );
require_api( 'user_api.php' );

class MonitorCommand extends Command {
	private $projectId;
	private $loggedInUserId;
	private $userIdsToAdd;

	function __construct( array $p_data ) {
		parent::__construct( $p_data );
	}

	function validate() {
		# Validate issue id
		if( !isset( $this->data['issue_id'] ) ) {
			throw new CommandException( HTTP_STATUS_BAD_REQUEST, 'issue_id missing', ERROR_GPC_VAR_NOT_FOUND );
		}

		if( !is_numeric( $this->data['issue_id'] ) ) {
			throw new CommandException( HTTP_STATUS_BAD_REQUEST, 'issue_id must be a valid issue id', ERROR_GPC_NOT_NUMBER );
		}
		
		$t_issue_id = (int)$this->data['issue_id'];

		if( !bug_exists( $t_issue_id ) ) {
			throw new CommandException( HTTP_STATUS_NOT_FOUND, "Issue id {$t_issue_id} not found", ERROR_BUG_NOT_FOUND );
		}

		$this->projectId = bug_get_field( $t_issue_id, 'project_id' );
		$t_logged_in_user = auth_get_current_user_id();

		# Validate user id (if specified), otherwise set from context
		if( !isset( $this->data['users'] ) ) {
			if( !auth_is_user_authenticated() ) {
				throw new CommandException( HTTP_STATUS_BAD_REQUEST, 'user_id missing', ERROR_GPC_VAR_NOT_FOUND );
			}

			$this->data['users'] = array( 'id' => auth_get_current_user_id() );
		}

		# Normalize user objects
		$t_user_ids = array();
		foreach( $this->data['users'] as $t_user ) {
			$t_user_id = $this->getIdForUser( $t_user );
			
			# TODO: If we throw exception above, then this check will not be necessary
			if( $t_user_id ) {
				$t_user_ids[] = $t_user_id;
			}
		}

		$this->userIdsToAdd = array();
		foreach( $t_user_ids as $t_user_id ) {
			if( user_is_anonymous( $t_user_id ) ) {
				# TODO: trigger exception
				# trigger_error( ERROR_PROTECTED_ACCOUNT, E_USER_ERROR );
				continue;
			}
		
			if( $t_logged_in_user == $t_user_id ) {
				$t_access_level_config = 'monitor_bug_threshold';
			} else {
				$t_access_level_config = 'monitor_add_others_bug_threshold';
			}

			$t_access_level = config_get(
				$t_access_level_config,
				/* default */ null,
				/* user */ null,
				$this->projectId );

			if( !access_has_bug_level( $t_access_level, $t_issue_id ) ) {
				# TODO: trigger error
				continue;
			}

			$this->userIdsToAdd[] = $t_user_id;
		}
	}

	protected function process() {
		if( $this->projectId != helper_get_current_project() ) {
			# in case the current project is not the same project of the bug we are
			# viewing, override the current project. This to avoid problems with
			# categories and handlers lists etc.
			$g_project_override = $this->projectId;
		}
		
		foreach( $this->userIdsToAdd as $t_user_id ) {
			bug_monitor( $this->data['issue_id'], $t_user_id );
		}
	}

	private function getIdForUser( array $p_user ) {
		# TODO: move to a common utility method that replaced this method
		# and mci_get_user_id()

		$t_identifier = '';
		if( isset( $p_user['id'] ) ) {
			$t_user_id = $p_user['id'];
		} else if( isset( $p_user['name'] ) ) {
			$t_identifier = $p_user['name'];
			$t_user_id = user_get_id_by_name( $p_user['name'] );
		} else if( isset( $p_user['real_name'] ) ) {
			$t_identifier = $p_user['real_name'];
			$t_user_id = user_get_id_by_realname( $p_user['real_name'] );
		} else if( isset( $p_user['name_or_realname' ] ) ) {
			$t_identifier = $p_user['name_or_realname'];
			$t_user_id = user_get_id_by_name( $p_user['name_or_realname'] );
			if( !$t_user_id ) {
				$t_user_id = user_get_id_by_realname( $p_user['name_or_realname'] );
			}	
		}

		if( !$t_user_id ) {
			# TODO: throw exception equivalent to below error
			# error_parameters( $t_identifier );
			# trigger_error( ERROR_USER_BY_NAME_NOT_FOUND, E_USER_ERROR );
			return false;
		}

		if( !user_exists( $t_user_id ) ) {
			# TODO: trigger error
			return false;
		}

		return $t_user_id;
	}
}
