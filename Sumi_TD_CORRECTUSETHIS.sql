--TD One Value
SELECT
    COUNT(*) AS DisruptionCount,
    SUM(de.RecoveryDays) AS TD
FROM (
    SELECT
        e.EventId,
        DATEDIFF(e.EventRecoveryDate, e.EventDate) AS RecoveryDays
    FROM DisruptionEvent AS e
    WHERE
        e.EventDate <= '2025-12-31' AND e.EventRecoveryDate >= '2024-01-01'
        AND EXISTS (
            SELECT ic.EventID
            FROM ImpactsCompany AS ic
            JOIN Company  AS c ON c.CompanyId  = ic.AffectedCompanyID
            JOIN Location AS l ON l.LocationId = c.LocationId
            WHERE ic.EventId = e.EventId
              AND ('Haynes-Long' = '' OR c.CompanyName   = 'Haynes-Long')
              AND ('' = '' OR l.CountryName   = '')
              AND ('' = '' OR l.ContinentName = '')
              AND ('' = '' OR c.TierLevel     = ''))
    GROUP BY e.EventId
) AS de;

--ART One Value
SELECT
    COUNT(*) AS DisruptionCount,
    AVG(de.RecoveryDays) AS ART
FROM (
    SELECT
        e.EventId,
        DATEDIFF(e.EventRecoveryDate, e.EventDate) AS RecoveryDays
    FROM DisruptionEvent AS e
    WHERE
        e.EventDate <= '2025-12-31' AND e.EventRecoveryDate >= '2024-01-01'
        AND EXISTS (
            SELECT ic.EventID
            FROM ImpactsCompany AS ic
            JOIN Company  AS c ON c.CompanyId  = ic.AffectedCompanyID
            JOIN Location AS l ON l.LocationId = c.LocationId
            WHERE ic.EventId = e.EventId
              AND ('Haynes-Long' = '' OR c.CompanyName   = 'Haynes-Long')
              AND ('' = '' OR l.CountryName   = '')
              AND ('' = '' OR l.ContinentName = '')
              AND ('' = '' OR c.TierLevel     = ''))
    GROUP BY e.EventId
) AS de
