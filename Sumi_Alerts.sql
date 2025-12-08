SELECT
    e.EventId,
    e.EventDate,
    e.EventRecoveryDate,
    dc.CategoryName AS EventType
FROM DisruptionEvent AS e
JOIN DisruptionCategory AS dc
    ON dc.CategoryID = e.CategoryID
WHERE
    e.EventRecoveryDate IS NULL
    OR e.EventRecoveryDate > CURRENT_DATE
ORDER BY e.EventDate ASC;
