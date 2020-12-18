<?php
namespace OCA\MyShares\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Share extends Entity implements JsonSerializable {
	protected $uidOwner;
	protected $itemType;
	protected $fileTarget;
	protected $token;
	protected $shareType;
	protected $shareWith;
	protected $password;
	protected $uidInitiator;
	protected $itemSource;
	protected $itemTarget;
	protected $permissions;
	protected $stime;
	protected $accepted;
	protected $expiration;
	protected $mailSend;
	protected $shareName;
	protected $parent;
	protected $note;
	protected $hideDownload;
	protected $label;
	protected $passwordByTalk;
	protected $fileSource;
	
	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
		   'token' => $this->token,
			'uid_owner' => $this->uidOwner,
		   'item_type' => $this->itemType,
		   'file_target' => $this->fileTarget,
			'share_type' => $this->shareType,
		];
	}
}
