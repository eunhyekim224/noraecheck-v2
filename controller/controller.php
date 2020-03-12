<?php
    require_once("./model/MemberManager.php");
    require_once("./model/PlaylistManager.php");
    
    use \Wcoding\Noraecheck\Model\MemberManager;
    use \Wcoding\Noraecheck\Model\PlaylistManager;

    function showLandingPage() {
        header();
    }
    
    function signUp($email, $username, $password) {
        //addMember
    }

    function logIn($username, $password) {
        //getMember
    }

    function makePlaylist($memberId, $name, $songs) {
        //addPlaylist
    }

    function showAllPlaylists($memberId) {
        $playlistManager = new PlaylistManager;
        $playlists = $playlistManager->getAllPlaylists($memberId);
        require("view/homeView.php");
    }

    function showPlaylist($memberId, $name) {
        //getPlaylist
    }


