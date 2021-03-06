<?php

function addCardIn($userID, $cardName, $cardImage) {
	global $db;

	$query = $db->prepare('INSERT INTO cards(us_id, ca_name, ca_image)
					VALUES(:userID, :cardName, :cardImage)');
	$query->execute(array('userID' => $userID,
						'cardName' => $cardName,
						'cardImage' => $cardImage));
	$query->closeCursor();
	return $db->lastInsertId();
}

function addCardInDeck($cardID, $deckID) {
	global $db;

	$query = $db->prepare('INSERT INTO cards_decks(ca_id, de_id)
					VALUES(:cardID, :deckID)');
	$query->execute(array('cardID' => $cardID,
						'deckID' => $deckID));
	$query->closeCursor();
}

function getCardInfos($cardID, $fields = array('*')) {
	global $db;

	$fields = implode(',', $fields);

	$query = $db->prepare('SELECT '.$fields.'
						FROM cards
						WHERE ca_id = ?');
	$query->execute(array($cardID));
	return $query->fetch(PDO::FETCH_ASSOC);
}

function getUserCards($userID) {
	global $db;

	$query = $db->prepare('SELECT *
						FROM cards
						WHERE us_id = ?');
	$query->execute(array($userID));

	return $query->fetchAll(PDO::FETCH_ASSOC);
}

//Permet de savoir à qui appartient une carte du tableau pour un tour donné
function getCardOwner($cardID, $turnID) {
	global $db;

	$query = $db->prepare('SELECT h.us_id 
						FROM boards as b
						INNER JOIN hands as h
						ON h.tu_played_id = b.tu_id AND h.ca_id = b.ca_id
						WHERE b.tu_id = :turnID
						AND b.ca_id = :cardID');
	$query->execute(array('turnID' => $turnID,
						'cardID' => $cardID));
	return $query->fetch(PDO::FETCH_ASSOC);
}

function getCardsInBoard($turnID) {
	global $db;

	$query = $db->prepare('SELECT boards.ca_id
						FROM boards
						WHERE tu_id = ?');
	$query->execute(array($turnID));
	return $query->fetchAll(PDO::FETCH_ASSOC);
}

function getStorytellerCardInBoard($turnID) {
	global $db;

	$query = $db->prepare('SELECT b.ca_id 
					FROM boards as b
					INNER JOIN hands as h
					ON h.tu_played_id = b.tu_id AND h.ca_id = b.ca_id
					INNER JOIN turns as t
					ON h.us_id = t.us_id
					WHERE b.tu_id = ?');
	$query->execute(array($turnID));
	return $query->fetch(PDO::FETCH_ASSOC);
}

function getCardsInHand($userID, $turnID, $gameID) {
	global $db;

	$query = $db->prepare('SELECT c.ca_id, c.ca_name, c.ca_image
						FROM cards as c
						INNER JOIN hands as h
						ON h.ca_id = c.ca_id
						INNER JOIN turns as t
						ON t.tu_id = h.tu_id
						INNER JOIN games as g
						ON g.ga_id = t.ga_id
						WHERE h.us_id = :userID
						AND h.tu_id <= :turnID
						AND h.tu_played_id IS NULL
						AND g.ga_id = :gameID');
	$query->execute(array('userID' => $userID,
						'turnID' => $turnID,
						'gameID' => $gameID));

	return $query->fetchAll(PDO::FETCH_ASSOC);
}

function isAlreadyInBoard($cardID, $turnID) {
	global $db;

	$query = $db->prepare('SELECT ca_id
						FROM boards
						WHERE ca_id = :cardID
						AND tu_id = :turnID');
	$query->execute(array('cardID' => $cardID,
						'turnID' => $turnID));
	return is_array($query->fetch(PDO::FETCH_ASSOC));
}


function updatePlayedTurn($cardID, $userID, $turnID) {
	global $db;

	$query = $db->prepare('UPDATE hands
						SET tu_played_id = :turnID
						WHERE ca_id = :cardID
						AND us_id = :userID
						AND tu_played_id IS NULL');
	return $query->execute(array('cardID' => $cardID,
							'userID' => $userID,
							'turnID' => $turnID));
}

function addCardInBoard($cardID, $turnID) {
	global $db;

	$query = $db->prepare('INSERT INTO boards(ca_id, tu_id)
						VALUES(:cardID, :turnID)');
	$query->execute(array('cardID' => $cardID,
						'turnID' => $turnID));
}

function addTurnComment($turnID, $comment) {
	global $db;

	$query = $db->prepare('UPDATE turns
						SET tu_comment = :comment
						WHERE tu_id = :turnID');
	$query->execute(array('comment' => $comment,
						'turnID' => $turnID));
}

function getPlayerCardInBoard($turnID, $userID) {
	global $db;

	$query = $db->prepare('SELECT b.ca_id
						FROM boards as b
						INNER JOIN hands as h
						ON h.ca_id = b.ca_id
						INNER JOIN turns as t
						ON t.tu_id = h.tu_played_id
						INNER JOIN users as u
						ON u.us_id = h.us_id
						WHERE h.tu_played_id = :turnID
						AND u.us_id = :userID');
	$query->execute(array('turnID' => $turnID,
						'userID' => $userID));
	return $query->fetch(PDO::FETCH_ASSOC);
}

function addGameVote($userID, $cardID, $turnID) {
	global $db;

	$query = $db->prepare('INSERT INTO votes(us_id, ca_id, tu_id)
						VALUES(:userID, :cardID, :turnID)');
	$query->execute(array('userID' => $userID,
						'cardID' => $cardID,
						'turnID' => $turnID));
}

function updateGameVote($userID, $cardID, $turnID) {
	global $db;

	$query = $db->prepare('UPDATE votes
							SET ca_id = :cardID
							WHERE us_id = :userID
							AND tu_id = :turnID');
	$query->execute(array('userID' => $userID,
						'cardID' => $cardID,
						'turnID' => $turnID));
}

function getUserVotedCardInTurn($turnID, $userID) {
	global $db;

	$query = $db->prepare('SELECT ca_id FROM votes
						WHERE us_id = :userID
						AND tu_id = :turnID');
	$query->execute(array('userID' => $userID,
						'turnID' => $turnID));
	return $query->fetch(PDO::FETCH_ASSOC);
}

function getCardVoteInTurn($cardID, $turnID) {
	global $db;

	$query = $db->prepare('SELECT us_id
						FROM votes
						WHERE ca_id = :cardID
						AND tu_id = :turnID');
	$query->execute(array('cardID' => $cardID,
						'turnID' => $turnID));
	return $query->fetchAll(PDO::FETCH_ASSOC);
}

function countCardVoteInTurn($cardID, $turnID) {
	global $db;

	$query = $db->prepare('SELECT COUNT(us_id) as nbVotes
						FROM votes
						WHERE ca_id = :cardID
						AND tu_id = :turnID');
	$query->execute(array('cardID' => $cardID,
						'turnID' => $turnID));
	return $query->fetch(PDO::FETCH_ASSOC);
}

function getDiscardedCards($gameID, $turnID) {
	global $db;

	$query = $db->prepare('SELECT DISTINCT(h.ca_id)
			FROM hands as h
			INNER JOIN turns as t
			ON t.tu_id = h.tu_id
			INNER JOIN games as g
			ON g.ga_id = t.ga_id
			WHERE h.tu_played_id IS NOT NULL
			AND g.ga_id = :gameID
			AND h.ca_id NOT IN (SELECT c.ca_id
								FROM cards as c
								INNER JOIN hands as h
								ON h.ca_id = c.ca_id
								INNER JOIN turns as t
								ON t.tu_id = h.tu_id
								INNER JOIN games as g
								ON g.ga_id = t.ga_id
								WHERE
						 		h.tu_id <= :turnID
								AND h.tu_played_id IS NULL
								AND g.ga_id = :gameID)');
	$query->execute(array('gameID' => $gameID,
							'turnID' => $turnID));
	return $query->fetchAll(PDO::FETCH_ASSOC);
}

function getTotalCardVoteInTurn($cardID, $turnID) {
	global $db;

	$query = $db->prepare('SELECT COUNT(us_id) as total
						FROM votes
						WHERE ca_id = :cardID
						AND tu_id = :turnID');
	$query->execute(array('cardID' => $cardID,
						'turnID' => $turnID));
	return $query->fetch(PDO::FETCH_ASSOC);
}

function shiftPick($gameID) {
	global $db;

	$query = $db->prepare('SELECT ca_id
						FROM pick
						WHERE ga_id = ?
						ORDER BY pi_order
						LIMIT 1');
	$query->execute(array($gameID));
	$results = $query->fetch(PDO::FETCH_ASSOC);

	$query->closeCursor();

	$query = $db->prepare('DELETE
						FROM pick
						WHERE ca_id = :cardID
						AND ga_id = :gameID');
	$query->execute(array('cardID' => $results['ca_id'],
						'gameID' => $gameID));

	return $results['ca_id'];
}

function addCardInHand($turnID, $cardID, $userID) {
	global $db;

	$query = $db->prepare('INSERT into hands(ca_id, us_id, tu_id)
						VALUES(:cardID, :userID, :turnID)');
	$query->execute(array('cardID' => $cardID,
						'userID' => $userID,
						'turnID' => $turnID));
}

function getPick($gameID) {
	global $db;

	$query = $db->prepare('SELECT ca_id
						FROM pick
						WHERE ga_id = ?
						ORDER BY pi_order');
	$query->execute(array($gameID));

	return $query->fetchAll(PDO::FETCH_ASSOC);
}

function savePick($gameID, $order, $cardID) {
	global $db;

	$query = $db->prepare('INSERT INTO pick(ga_id, ca_id, pi_order)
						VALUES(:gameID, :cardID, :order)');
	$query->execute(array('cardID' => $cardID,
						'gameID' => $gameID,
						'order' => $order));
	$query->closeCursor();
}