<?php
  namespace jdRoll\service;

/**
 * Notification Center
 *
 * @package notificationService
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */

class NotificationService {

  private $db;
  private $logger;
  private $topicService;
  private $campagneService;
  private $userService;
  private $mailer;
  private $favorisService;

  public function __construct($db, $logger, $requestStack, $userService, $topicService, $campagneService, $favorisService, $mailer) {
    $this->db = $db;
    $this->logger = $logger;
    $this->userService = $userService;
    $this->topicService = $topicService;
    $this->campagneService = $campagneService;
    $this->favorisService = $favorisService;
    $this->mailer = $mailer;
    $request = $requestStack->getCurrentRequest();
    $this->baseUrl = $request->getScheme() . '://' . $request->getHttpHost();
  }

  public function getNotifForUser($user_id) {
    $sql = "SELECT notif.*, campagne.name as game
                FROM notif
                LEFT JOIN topics
                ON topics.id = notif.target_id
                AND notif.type = 'MSG'
                LEFT JOIN sections
                ON sections.id = topics.section_id
                LEFT JOIN campagne
                ON (campagne.id = sections.campagne_id
                OR (
                    campagne.id = notif.target_id
                    AND (
                        notif.type = 'JOIN'
                        OR notif.type = 'QUIT'
                        OR notif.type = 'PERSO'
                        )
                    )
                )
                WHERE notif.user_id = :user

                ORDER BY game, notif.id DESC;";
    return $this->db->fetchAll($sql, array('user' => $user_id));
  }

  public function deleteNotif($id) {
    $sql = "DELETE FROM notif WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue("id", $id);
    $stmt->execute();
  }

  /**
     * @param string $title
     * @param string $type
     */
  public function sentNotif($user_id, $title, $content, $url, $type, $target_id) {
    $nbNotif = 0;
    $notifContent = str_replace('__URL__', $url, $content);

    // FIXIT - Contournement mis en place suite à des urls différentes accédant à la même plateforme (Bug Killian)
    $url = str_replace("/jdRoll/", "/", $url);
    $content = str_replace("/jdRoll/", "/", $content);

    if( ($type == "MSG" || $type == "DICE") || ($type == "PERSO") ) {
      $sql = "SELECT count(*) as nb
            FROM notif
            WHERE
                user_id = :user
            AND type = :type
            AND target_id = :target_id";

      $nbNotif = $this->db->fetchColumn($sql, array('user' => $user_id, 'type' => $type, 'target_id' => $target_id), 0);
    }

    if( $nbNotif == 0 ) {
      $sql = "INSERT INTO notif (user_id, title, content, url, type, target_id) VALUES (:user, :title, :content, :url, :type, :target_id)";
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue("user", $user_id);
      $stmt->bindValue("title", $title);
      $stmt->bindValue("content", $notifContent);
      $stmt->bindValue("url", $url);
      $stmt->bindValue("type", $type);
      $stmt->bindValue("target_id", $target_id);
      $stmt->execute();
    } else {
      $sql = "UPDATE notif
                    SET nb = nb + 1
                    WHERE
                        user_id = :user
                    AND type = :type
                    AND target_id = :target_id
                    ";
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue("user", $user_id);
      $stmt->bindValue("type", $type);
      $stmt->bindValue("target_id", $target_id);
      $stmt->execute();
    }
  }

  public function sendNotifMail($user, $title, $content, $url, $type, $target_id) {
    $mailContent = str_replace('__URL__', $this->baseUrl . $url, $content);
    try {
      $message = \Swift_Message::newInstance()
        ->setSubject('[JdRoll] Notification - ' . $title)
        ->setFrom(array('contact@jdroll.org'))
        ->setTo(array($user['mail']))
        ->setBody($mailContent, 'text/html');

      $this->mailer->send($message);
    } catch(Exception $e) {
      // Pas de mail, tant pis...
    }

  }

  public function alertUserForMp($expediteur, $destinataires, $msgTitle, $url) {
    $title = "Nouveau message privé";
    $content = "$expediteur a envoyé un mp du titre de <a href='__URL__'>$msgTitle</a>";

    foreach ($destinataires as $destinaire) {
      $user = $this->userService->getByUsername($destinaire);

      if($user['notif_mp'] == 1) {
        $this->sentNotif($user['id'], $title, $content, $url, 'MP', 0);
      }

      if($user['mail_mp'] == 1) {
        $this->sendNotifMail($user, $title, $content, $url, 'MP', 0);
      }
    }
  }

  public function alertModifPerso($user_id, $perso, $campagne_id, $urlPj, $urlMj) {
    $persoUser = $perso['user_id'];
    $campagne = $this->campagneService->getCampagne($campagne_id);
    $title = "Modification de personnage - " . $campagne['name'];
    $mj = $campagne['mj_id'];
    if($mj != $user_id) {
      $user = $this->userService->getById($mj);
      $content = "Le personnage <a href='$urlMj'>" . $perso['name'] . "</a> a été modifié par le joueur.";

      if($user['notif_perso'] == 1) {
        $this->sentNotif($mj, $title, $content, $urlMj, 'PERSO', $campagne_id);
      }
      if($user['mail_perso'] == 1) {
        $this->sendNotifMail($user, $title, $content, $urlMj, 'PERSO', $campagne_id);
      }
    }
    if($persoUser != $user_id) {
      $user = $this->userService->getById($persoUser);
      $content = "Le personnage <a href='$urlPj'>" . $perso['name'] . "</a> a été modifié par le maître de jeu.";

      if($user['notif_perso'] == 1) {
        $this->sentNotif($persoUser, $title, $content, $urlPj, 'PERSO', $campagne_id);
      }
      if($user['mail_perso'] == 1) {
        $this->sendNotifMail($user, $title, $content, $urlPj, 'PERSO', $campagne_id);
      }
    }
  }

  public function alertJoinCampagne($campagne, $joueur, $url) {
    $user = $campagne['mj_id'];
    $user_obj = $this->userService->getById($user);
    $title = "Nouvelle inscription - " . $campagne['name'];

    if($user_obj['notif_inscription'] == 1) {
      $content = "$joueur s'est inscrit sur <a href='__URL__'>la partie.</a>";
      $this->sentNotif($user, $title, $content, $url, 'JOIN', 0);
    }
    if($user_obj['mail_inscription'] == 1) {
      $content = "$joueur s'est inscrit sur <a href='__URL__'>la partie.</a>";
      $this->sendNotifMail($user_obj, $title, $content, $url, 'JOIN', 0);
    }
  }

  public function alertQuitCampagne($campagne, $joueur, $url) {
    $user = $campagne['mj_id'];
    $user_obj = $this->userService->getById($user);
    $title = "Désinscription - " . $campagne['name'];
    $content = "$joueur s'est désinscrit sur <a href='__URL__'>la partie.</a>";

    if($user_obj['notif_inscription'] == 1) {
      $this->sentNotif($user, $title, $content, $url, 'QUIT', 0);
    }
    if($user_obj['mail_inscription'] == 1) {
      $this->sendNotifMail($user_obj, $title, $content, $url, 'QUIT', 0);
    }
  }

  public function insertNotifPost($user_id, $title, $content, $url, $type, $target_id) {
    $user = $this->userService->getById($user_id);
    if($user['notif_message'] == 1) {
      $this->sentNotif($user_id, $title, $content, $url, $type, $target_id);
    }
    if($user['mail_message'] == 1) {
      $this->sendNotifMail($user, $title, $content, $url, $type, $target_id);
    }
  }

  public function insertNotifDice($user_id, $title, $content, $url, $type, $target_id) {
    $user = $this->userService->getById($user_id);
    if($user['notif_dice'] == 1) {
      $this->sentNotif($user_id, $title, $content, $url, $type, $target_id);
    }
    if($user['mail_dice'] == 1) {
      $this->sendNotifMail($user, $title, $content, $url, $type, $target_id);
    }
  }

  public function alertDiceInCampagne($user_id, $campagne_id, $topic_id, $url) {
    $campagne = $this->campagneService->getCampagne($campagne_id);
    $user = $this->userService->getById($user_id);
    $topic = $this->topicService->getTopic($topic_id);
    $content = "";
    if($topic_id > 0) {
      $content = "Lancer de dé de " . $user['username'] . " dans le sujet <a href='__URL__'>" . $topic['title'] . "</a>";
    } else {
      $content = "Lancer de dé de " . $user['username'] . " via le lanceur de dé <a href='__URL__'>de la campagne</a>";
    }
    $title = "Lancer de dé - " . $campagne['name'];
    if($user_id != $campagne['mj_id']) {
      $this->insertNotifDice($campagne['mj_id'], $title, $content, $url, 'DICE', $topic_id);
    }
  }

  public function alertPostInCampagne($user_id, $campagne_id, $topic_id, $url) {
    if($campagne_id != 0) {
      $user = $this->userService->getById($user_id);
      $campagne = $this->campagneService->getCampagne($campagne_id);
      $participants = $this->campagneService->getParticipant($campagne_id);
      $favoris = $this->favorisService->getFavorisInCampagne($campagne_id);
      $topic = $this->topicService->getTopic($topic_id);
      $content = "Nouveau message de " . $user['username'] . " dans le sujet <a href='__URL__'>" . $topic['title'] . "</a>";
      $title = "Nouveau message - " . $campagne['name'];
      if($topic['is_private'] != 1) {
        // FIXIT Arma - Capitalisation
        foreach($participants as $participant) {
          if($user_id != $participant['id']) {
            $this->insertNotifPost($participant['id'], $title, $content, $url, 'MSG', $topic_id);
          }
        }
        foreach($favoris as $participant) {
          if($user_id != $participant['user_id']) {
            $this->insertNotifPost($participant['user_id'], $title, $content, $url, 'MSG', $topic_id);
          }
        }
      } else {
        $participants = $this->topicService->getWhoCanRead($topic_id, $campagne_id);
        foreach($participants as $participant) {
          if($user_id != $participant['user_id']) {
            $this->insertNotifPost($participant['user_id'], $title, $content, $url, 'MSG', $topic_id);
          }
        }
      }
      if($user_id != $campagne['mj_id']) {
        $this->insertNotifPost($campagne['mj_id'], $title, $content, $url, 'MSG', $topic_id);
      }

    }
  }

}

?>
