<?php
    require_once("./model/MemberManager.php");
    require_once("./model/PlaylistManager.php");
    require_once("./model/SongManager.php");

    // use \Wcoding\Noraecheck\Model\MemberManager;
    // use \Wcoding\Noraecheck\Model\PlaylistManager;

    function showLandingPage($error,$status) {
        require("view/landing.php");
    }
    
    function signUp($email, $username, $password, $passwordConf,$error) {
        $memberManager = new MemberManager();
        if($username AND $password AND $passwordConf AND $email){
            $usernameInUse = $memberManager->getMember($username);
            if(!$usernameInUse){
                if(preg_match("#^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$#",$email)){
                    if ($username AND $password == $passwordConf){
                        $status = $memberManager->addMember($email,$username,$password);
                        header("location:index.php?action=login&success=1");
                    } else if ($password != $passwordCheck){
                        header('Location: index.php?action=register&error=passError');
                    }
                } else {
                    header('Location: index.php?action=register&error=mailError');
                }
            } else{
                header('Location: index.php?action=register&error=logOld');
            }
        } else {
            // require("view/landingSignup.php");
            require("view/landing.php");
        }  
    }

    function logIn($username,$password,$error,$status) {
        $loginManager = new MemberManager();
        if($username AND $password){
            $userInfo = $loginManager->getMember($username);
            //getMember confirms userId, password is checked below
            if($userInfo){
                if (password_verify($password, $userInfo['password'])){
                        $_SESSION['username'] = $userInfo['username'];
                        $_SESSION['memberId'] = $userInfo['id'];
                        header('Location: index.php?action=showMyList');
                } else {
                    $error = 'passError';
                    require("view/landing.php");
                }
            } else {
                $error = 'logError';
                require("view/landing.php");
            }
        }else{
            $error = 'missingField';
            require("view/landing.php");
        }
    }

    function makePlaylist($memberId, $name) {
        $playlistManager = new PlaylistManager();
        $playlists = $playlistManager->addPlaylist($memberId, $name);
        header('Location: index.php?action=showMyList');
    }

    function showAllPlaylists($memberId) {
        $playlistManager = new PlaylistManager();
        $playlists = $playlistManager->getAllPlaylists($memberId);
        $displayMode = 'playlists';
        require("view/home.php");    
    }

    function showSongs($playlistId) {
        $songManager = new SongManager();
        $songDisplay = $songManager->getSongs($playlistId);
        $displayMode = 'songs';
        require("view/home.php");
    }

    function deletePlaylist($playlistId, $memberId) {
        $playlistManager = new PlaylistManager();
        $playlistManager->deletePlaylist($playlistId);
        header('Location: index.php?action=showMyList');
    }

    function deleteSong($songId) {
        $songManager = new SongManager();
        $data = $songManager->deleteSong($songId);
        header('Location: index.php?action=showMySongs&playlistId='.$data['playlistId'].'&songCount='.$data['songCount']);
    }

    function search($memberId,$searchCache,$categoryCache) {
        $playlistAddManager = new PlaylistManager();
        $playlistsAdd = $playlistAddManager->getAllPlaylists($memberId);
        $modalDisplay = 'off';
        require("view/search.php");
    }
    function searchModal($song,$singer,$tj,$kumyoung,$searchCache,$categoryCache,$memberId,$playlistId) {
        $playlistAddManager = new PlaylistManager();
        $playlistsAdd = $playlistAddManager->getAllPlaylists($memberId);
        $modalDisplay = 'on';
        require("view/search.php");
    }
    function addToPlaylist($playlistId,$singer,$song,$tj,$kumyoung) {
        $songAddManager = new SongManager();
        $songAdd = $songAddManager->addSong($playlistId, $singer, $song, $tj, $kumyoung);
        header('Location: index.php?action=search');
    }

    function addSongToNewPlaylist($memberId,$playlistName,$singer,$song,$tj,$kumyoung) {
        $playlistAddManager = new PlaylistManager();
        $newAddPlaylist = $playlistAddManager->addPlaylist($memberId, $playlistName);
        $getPlaylist = $playlistAddManager->getPlaylist($memberId, $playlistName);
        $gotPlaylist = $getPlaylist->fetch();
        $newPlaylistId = $gotPlaylist['playlistId'];
        $songAddManager = new SongManager();
        echo $playlistName .$singer .$song .$tj .$kumyoung;
        $songAdd = $songAddManager->addSong($newPlaylistId, $singer, $song, $tj, $kumyoung);
        header('Location: index.php?action=search');
    }
    


