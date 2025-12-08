SELECT
    EventId,
    EventDate,
    EventRecoveryDate
FROM DisruptionEvent
WHERE
    EventRecoveryDate IS NULL
    OR EventRecoveryDate > CURRENT_DATE
ORDER BY EventDate ASC;
