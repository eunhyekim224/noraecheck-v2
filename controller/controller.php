<?php
    require_once("./model/MemberManager.php");
    require_once("./model/PlaylistManager.php");
    require_once("./model/SongManager.php");
    require_once("./model/challengeManager.php");

    // use \Wcoding\Noraecheck\Model\MemberManager;
    // use \Wcoding\Noraecheck\Model\PlaylistManager;

    function showLandingPage($error,$status) {
        require("view/landing.php");
    }
    
    function signUp($email, $username, $password, $passwordConf) {
        $memberManager = new MemberManager();
        $errors = array(
            "contextUp" => "signUp"
        );
        
        $usernameInUse = $memberManager->getMember($username);

        if($username AND $password AND $passwordConf AND $email){
            // $usernameInUse = $memberManager->getMember($username);
            if(!$usernameInUse){
                if(preg_match("#^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$#",$email)){
                    if(preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$#",$password)) {
                        if ($username AND $password == $passwordConf){
                            $status = $memberManager->addMember($email,$username,$password);
                            header("location:index.php?action=login&success=1");
                        } else if ($password != $passwordCheck){
                            $errors['pwdConf'] = 'password does not match';
                        } 
                    } else {
                        $errors['pwd'] = 'please include 8 characters, upper/lower case letters, and digits';
                    }
                    
                } else {
                    $errors['email'] = 'incorrect email';
                    
                }
            } else{
                $errors['loginNew'] = 'username taken';
                
            }
        } else {
            // require("view/landingSignup.php");
            // require("view/landingSignIn.php"); 
            require("view/landing.php");
        }
        require("view/landing.php");  
    }

    function signIn($username,$password) {
        $loginManager = new MemberManager();
        $errors = array(
            "context" => "signIn"
        );
       
        $userInfo = $loginManager->getMember($username);
        //getMember confirms userId, password is checked below
        if($userInfo){
            if (password_verify($password, $userInfo['password'])){
                    $_SESSION['username'] = $userInfo['username'];
                    $_SESSION['memberId'] = $userInfo['id'];
                    header('Location: index.php?action=showMyList');
            } else {
                $errors['password'] = 'incorrect password';
            }
        } else {
            $errors['username'] = 'there are no accounts with that ID';
        }
        require("view/landing.php");
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
        $playlistManager = new PlaylistManager();
        $songManager = new SongManager();
        $mainPlaylist = $playlistManager->getMainPlaylist($playlistId);
        $songDisplay = $songManager->getSongs($playlistId);
        $displayMode = 'songs';
        require("view/home.php");
    }

    function editBrandCode($playlistId,$songId,$tjCode,$kumyoungCode) {
        $songManager = new SongManager();
        $editBrandCodes = $songManager->editBrandCodes($songId,$tjCode,$kumyoungCode);
        header('Location: index.php?action=showMySongs&playlistId='.$playlistId);
    }

    function editPlaylist($newPlaylistName,$playlistId) {
        $playlistManager = new PlaylistManager();
        $editPlaylist = $playlistManager->editPlaylistName($newPlaylistName,$playlistId);
        header('Location: index.php?action=showMySongs&playlistId='.$playlistId);
    }

    function deletePlaylist($playlistId, $memberId) {
        $playlistManager = new PlaylistManager();
        $playlistManager->deletePlaylist($playlistId);
        header('Location: index.php?action=showMyList');
    }

    function deleteSong($songId) {
        $songManager = new SongManager();
        $playlistId = $songManager->deleteSong($songId);
        header('Location: index.php?action=showMySongs&playlistId='.$playlistId);
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
        $songAddManager = new SongManager();
        $songAdd = $songAddManager->addSong($newAddPlaylist, $singer, $song, $tj, $kumyoung);
        header('Location: index.php?action=search');
    }

    function showChallenge($memberId) {
        $playlistManager = new PlaylistManager();
        $playlists = $playlistManager->getAllPlaylists($memberId);
        $displayMode = 'challengeSetUp';
        require("view/home.php");
    }
    function challengeInProgress($memberId,$round,$score) {
        $challengeInProgressManager = new ChallengeManager();
        $getChallenge = $challengeInProgressManager->getChallenge($memberId);
        $displayMode = 'challengeInProgress';
        require("view/home.php");
    }

    function updateScore($memberId,$score,$songId,$round){
        $updateScore = new ChallengeManager();
        $updatedScore = $updateScore->updateScore($memberId,$score,$songId);
        header('Location: index.php?action=challengeInProgress&score='.$updatedScore.'&round='.$round);
        echo $updatedScore;
    }

    function showProfile($memberId,$userName) {
        $profileManager = new MemberManager();
        $currentProfile = $profileManager->getMember($userName);
        $displayMode = 'profile';
        require("view/home.php");
    }

    function editProfile($memberId,$oldUsername,$newUsername,$email,$oldPwd,$newPwd,$newpwdConf){
        $memberManager = new MemberManager();
        $errors = array(
            "contextUp" => "editProfile"
        );
        
        $currentProfile = $memberManager->getMember($oldUsername);

        if($newUsername && $email && $oldPwd){
            if (password_verify($oldPwd, $currentProfile['password'])){
                if($currentProfile['username'] != $newUsername){
                    if(preg_match("#^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$#",$email)){
                        if ($newPwd && $newpwdConf) {
                            if(preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$#",$newPwd)) {
                                if ($newUsername && $newPwd && $newpwdConf && $newPwd == $newpwdConf){
                                    $status = $memberManager->editMember($memberId,$email,$newUsername,$newPwd);
                                    header("Location: index.php?success=1");
                                } else if ($newPwd && $newpwdConf && $newPwd != $newpwdConf){
                                    $errors['pwdConf'] = 'password does not match';
                                }
                            } else {
                                $errors['pwd'] = 'please include 8 characters, upper/lower case letters, and digits';
                            }   
                        } else {
                            $status = $memberManager->editMember($memberId,$email,$newUsername,$oldPwd);
                            header("Location: index.php?success=1");
                        }
                    } else {
                        $errors['email'] = 'incorrect email'; 
                    }
                } else{
                    $errors['loginNew'] = 'username taken'; 
                }
            } else {
                $errors['oldPwdConf'] = 'incorrect password';
            }
        } else {
            $errors['blanks'] = 'fill in the blanks';
        }
        $displayMode = 'profile';
        require("view/home.php");
    } 

    function deleteProfile($memberId) {
        $memberManager = new MemberManager();
        $deleteProfile = $memberManager->deleteProfile($memberId);
        header("Location: index.php?success=1");
    }

    function insertChallengeInfo($memberId,$allSingers,$chalPlaylistOptions,$chalPlaylistId,$noOfSongs,$scoreOption) {
        // echo '<strong>List of all singers: </strong>'.$allSingers;
        $singersArray = explode(',',$allSingers);

        if ($chalPlaylistOptions === 'allPlaylists') {
            $playlistsArray = array();
            $playlistManager = new PlaylistManager();
            $playlistsDb = $playlistManager->getAllPlaylists($memberId);
            
            while ($playlists = $playlistsDb->fetch()) {
                array_push($playlistsArray, $playlists['playlistId']);
            }

            $songsArray = array();
            $songManager = new SongManager();
            for ($i=0, $c=count($playlistsArray); $i<$c; $i++) {
                $songsDb = $songManager->getSongs($playlistsArray[$i]);
        
                while ( $songs = $songsDb->fetch()) {
                    array_push($songsArray, $songs['songName']);
                }
            }
            // print_r($songsArray);
            shuffle($songsArray);
            // print_r($songsArray);
            
            if ($noOfSongs >= count($songsArray)) {
                $songsChal = $songsArray;
            } else {
                $songsChal = array_slice($songsArray,0,$noOfSongs);
            }

            
            print_r($singersArray);
            echo '<br>';
            print_r($songsChal);

            $singerAndSongArray = array();

            if (count($singersArray) < count($songsChal)) {
                // while ($songsChal) {
                //     for ($i=0, $c=count($singersArray); $i<$c; $i++) {
                //         $singerAndSongArray[$singersArray[$i]] = $songsChal[$i];
                //         // print_r('<br>'.$singersArray[$i]);
                //         // print_r('<br>'.$songsChal[$i]);
                //     }
                //     array_shift($songsChal);
                //     shuffle($songsChal);
                // }
                // for ($i=count($songsChal), $c=0; $i>=$c; $i--) {
                //     for ($j=0, $k=count($singersArray); $j<$k; $j++) {
                //         $singerAndSongArray[$singersArray[$j]] = $songsChal[$j];
                //         // print_r('<br>'.$singersArray[$j]);
                //         // print_r('<br>'.$songsChal[$j]);
                //         shuffle($songsChal);
                //         // $singerAndSongArray = array_combine($singersArray,$songsChal);
                //     }
                //     // array_shift($songsChal);  
                // }

                foreach ($songsChal as $value2) {
                    foreach ($singersArray as $key => $value) {
                        echo '<br>'.$value2;
                        $singerAndSongArray[$value] = $value2;
                    }
                }
            }

            echo '<br>';
            print_r($singerAndSongArray);
            print_r('<br><strong>Number of songs: </strong>'.count($songsChal));
        } else if ($chalPlaylistOptions === 'onePlaylist') {
            echo '<br><strong>Option to choose songs from: </strong>'.$chalPlaylistOptions;
            echo '<br><strong>Playlist ID selected: </strong>'.$chalPlaylistId;
        }
        echo '<br><strong>Enter score option: </strong>'.$scoreOption;
    }

    function logout(){
        session_destroy();
        header("Location:index.php");
    }
    


