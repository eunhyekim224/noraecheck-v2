<?php
    require_once('model/Manager.php');

    /**
     * TODO: decide on how the songs are stored in the table
     * TODO: join playlist and member tables 
     * TODO: function to deletePlaylist
     * TODO: function to deleteAllPlaylists
     */

    class PlaylistManager extends Manager {
        public function addPlaylist($memberId, $name) {
            $db = $this->dbConnect();          
            $addMember = $db->prepare("INSERT INTO playlists(memberId, name) VALUES(:memberId, :name)");
            $status = $addMember->execute(array(
                'memberId' => $memberId,
                'name' => htmlspecialchars($name),
            ));
            if (!$status) {
                throw new PDOException('Unable to create the playlist!');
            } 
        }

        public function getAllPlaylists($memberId) {
            $db = $this->dbConnect();
            $playlists = $db->prepare('SELECT p.memberId AS memberId, m.username AS username, p.name AS playlistName, DATE_FORMAT(p.creationDate, "%d/%m/%Y") AS playlistCreationDate,
                                        (SELECT COUNT(s.id) FROM songs s WHERE s.playlistId = p.id) AS songCount
                                        FROM playlists p
                                        JOIN members m
                                        ON p.memberId = m.id
                                        WHERE p.memberId = :memberId ORDER BY creationDate DESC');
            $playlistsResp = $playlists->execute(array(
                'memberId' => $memberId
            ));
            if(!$playlistsResp) {
                throw new PDOException('Unable to retrieve all playlists!');
            }
            return $playlists;
        }
        
        public function getPlaylist($memberId, $name) {
            $db = $this->dbConnect();
            $playlist = $db->prepare('SELECT p.memberId AS memberId, m.username AS username, p.name AS playlistName, p.creationDate AS playlistCreationDate 
                                       FROM playlists p
                                       JOIN members m
                                       ON p.memberId = m.id
                                       WHERE p.memberId = :memberId AND p.name = :name');
            $resp = $playlists->execute(array(
                'memberId' => $memberId,
                'name' => $name
            ));
            if(!$resp) {
                throw new PDOException('Unable to retrieve this playlist!');
            }
            return $playlists;
        }
    }
