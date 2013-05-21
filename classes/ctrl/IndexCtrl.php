<?php
class IndexCtrl extends CtrlBase {

	public function index() {
		return DataView::getView('', 'html', 'index/index.php');
	}

	public function saveUser() {
		$username = $this->getString($this->params, 'username');
		$password = $this->getString($this->params, 'password');
		$userService = common\ClassLocator::getService('User');
		$userService->
	}

	public function login() {
		$username = $this->getString($this->params, 'username');
		$password = $this->getString($this->params, 'password');
		$userService = common\ClassLocator::getService('User');
		$token = $userService->checkLogin();

		if($token) {

		}else{

		}

	}


	public function getUser() {
		$userId = $this->getInteger($this->params, 'uid');
		$token = $this->getString($this->params, 'token');
		$userService = common\ClassLocator::getService('User');
		$userInfo = $this->fetchById($userId);
		if($userInfo->token === $token) {
			return DataView::getView($userInfo->getHash(), 'json');
		}
		throw new \Exception("token error");
	}

}
