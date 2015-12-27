USE beranem2;
DELETE FROM pastes
WHERE time < GETDATE() - 7
AND id_user = 0;
