-- Percent Contribution of Each Company Towards HDR
SELECT
    c.CompanyName, 
    l.ContinentName, 
    l.CountryName, 
    c.TierLevel,
    (COUNT(CASE WHEN ic.ImpactLevel = 'High' THEN 1 END) * 100.0 /
        NULLIF((
            SELECT COUNT(*) 
            FROM ImpactsCompany ic2
            JOIN DisruptionEvent de2 ON ic2.EventID = de2.EventID
            JOIN Company c2 ON ic2.AffectedCompanyID = c2.CompanyID
            JOIN Location l2 ON c2.LocationID = l2.LocationID
            WHERE de2.EventDate BETWEEN '2019-01-01' AND '2025-12-31'
              AND ic2.ImpactLevel = 'High'
              AND ('1' = '' OR c2.TierLevel = '1')
              AND ('' = '' OR l2.CountryName = '')
              AND ('' = '' OR l2.ContinentName = '')
              AND ('' = '' OR c2.CompanyName = '')
        ), 0)
    ) AS PercentContribution
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
ORDER BY PercentContribution DESC;
