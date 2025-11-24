-- Percent Contribution of Each Company Towards HDR
SELECT
    c.CompanyName, 
    l.ContinentName, 
    l.CountryName, 
    c.TierLevel,
    (COUNT(CASE WHEN ic.ImpactLevel = 'High' THEN 1 END) * 100.0 / NULLIF((
            SELECT COUNT(*) 
            FROM ImpactsCompany ic
            JOIN DisruptionEvent de ON ic.EventID = de.EventID
            JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
            JOIN Location l ON c.LocationID = l.LocationID
            WHERE de.EventDate BETWEEN '2019-01-01' AND '2025-12-31'
              AND ic.ImpactLevel = 'High'
              AND ('' = '' OR c.CompanyName = '')
              AND ('' = '' OR c.TierLevel = '')
              AND ('' = '' OR l.CountryName = '')
              AND ('' = '' OR l.ContinentName = '')         
        ), 0)
    ) AS Percent
FROM ImpactsCompany ic
JOIN DisruptionEvent de ON ic.EventID = de.EventID
JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
JOIN Location l ON c.LocationID = l.LocationID
WHERE
    de.EventDate BETWEEN '2019-01-01' AND '2025-12-31' 
    AND ('' = '' OR c.CompanyName = '')
    AND ('' = '' OR c.TierLevel = '')
    AND ('' = '' OR l.CountryName = '')
    AND ('' = '' OR l.ContinentName = '')
GROUP BY
    c.CompanyName, 
    l.ContinentName, 
    l.CountryName, 
    c.TierLevel
ORDER BY Percent DESC;

-- Overall HDR Calculation (one value)
SELECT
    (COUNT(CASE WHEN ic.ImpactLevel = 'High' THEN 1 END) * 100.0 / NULLIF(COUNT(ic.EventID), 0)) AS HDR
FROM ImpactsCompany ic
JOIN DisruptionEvent de ON ic.EventID = de.EventID
JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
JOIN Location l ON c.LocationID = l.LocationID
WHERE
    de.EventDate BETWEEN '2019-01-01' AND '2025-12-31'
    AND ('' = '' OR c.CompanyName = '')
    AND ('' = '' OR c.TierLevel = '')
    AND ('' = '' OR l.CountryName = '')
    AND ('' = '' OR l.ContinentName = '')
