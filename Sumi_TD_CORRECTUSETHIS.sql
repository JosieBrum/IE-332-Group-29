-- TD Value
SELECT
    de.CompanyName,
    de.TierLevel,
    de.ContinentName,
    de.CountryName,
    COUNT(*) AS DisruptionCount,
    SUM(de.DowntimeDays) AS TotalDowntimeDays
FROM (
    SELECT
        e.EventId,
        e.EventDate,
        e.EventRecoveryDate,
        c.CompanyId,
        c.CompanyName,
        c.TierLevel,
        l.CountryName,
        l.ContinentName,
        DATEDIFF(e.EventRecoveryDate, e.EventDate) AS DowntimeDays
    FROM DisruptionEvent AS e
    JOIN ImpactsCompany AS ic
      ON ic.EventId = e.EventId
    JOIN Company AS c
      ON c.CompanyId = ic.AffectedCompanyID
    JOIN Location AS l
      ON l.LocationId = c.LocationId
    WHERE
        e.EventDate <= '2025-12-31' AND e.EventRecoveryDate >= '2024-01-01'
        AND ('Haynes-Long' = '' OR c.CompanyName   = 'Haynes-Long')
        AND (''             = '' OR l.CountryName   = '')
        AND (''             = '' OR l.ContinentName = '')
        AND (''             = '' OR c.TierLevel     = '')
) AS de
GROUP BY
    de.CompanyName,
    de.TierLevel,
    de.ContinentName,
    de.CountryName;
