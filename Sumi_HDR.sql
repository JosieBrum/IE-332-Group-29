
SELECT
    c.CompanyName, 
    l.ContinentName, 
    l.CountryName, 
    c.TierLevel,
    (COUNT(CASE WHEN ic.ImpactLevel = 'High' THEN 1 END) * 100.0 / NULLIF(COUNT(ic.EventID), 0)) AS HDR,
    (COUNT(CASE WHEN ic.ImpactLevel = 'High' THEN 1 END) * 100.0 /
        NULLIF((
            SELECT COUNT(*) 
            FROM ImpactsCompany ic
            JOIN DisruptionEvent de ON ic.EventID = de.EventID
            JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
            JOIN Location l ON c.LocationID = l.LocationID
            WHERE de.EventDate BETWEEN '2019-01-01' AND '2025-12-31'
              AND ic.ImpactLevel = 'High'
              AND ('1' = '' OR c.TierLevel = '1')
              AND ('' = '' OR l.CountryName = '')
              AND ('' = '' OR l.ContinentName = '')
              AND ('' = '' OR c.CompanyName = '')
        ), 0)
    ) AS Percent
FROM ImpactsCompany ic
JOIN DisruptionEvent de ON ic.EventID = de.EventID
JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
JOIN Location l ON c.LocationID = l.LocationID
WHERE
    de.EventDate BETWEEN '2019-01-01' AND '2025-12-31' 
    AND ('' = '' OR c.CompanyName = '')
    AND ('1' = '' OR c.TierLevel = '1')
    AND ('' = '' OR l.CountryName = '')
    AND ('' = '' OR l.ContinentName = '')
GROUP BY
    c.CompanyName, 
    l.ContinentName, 
    l.CountryName, 
    c.TierLevel
