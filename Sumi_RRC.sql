-- RRC by Continent
SELECT
    l.CountryName,
    COUNT(DISTINCT de.EventID) * 100.0 / total.TotalEvents AS RRC
FROM DisruptionEvent de
JOIN ImpactsCompany ic ON ic.EventID = de.EventID
JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
JOIN Location l ON c.LocationID = l.LocationID
JOIN (
    SELECT COUNT(DISTINCT de2.EventID) AS TotalEvents
    FROM DisruptionEvent de2
        de.EventDate <= '2025-12-31' and de.EventRecoveryDate >= '2019-01-01'
    AND ('' = '' OR c.TierLevel = '')
) AS total
WHERE
        de.EventDate <= '2025-12-31' and de.EventRecoveryDate >= '2019-01-01'
    AND ('' = '' OR c.TierLevel = '')
GROUP BY
    l.ContinentName,
    total.TotalEvents
ORDER BY
    RRC DESC

SELECT
    l.CountryName,
    COUNT(DISTINCT de.EventID) * 100.0 / total.TotalEvents AS RRC
FROM DisruptionEvent de
JOIN ImpactsCompany ic ON ic.EventID = de.EventID
JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
JOIN Location l ON c.LocationID = l.LocationID
CROSS JOIN (
    SELECT COUNT(DISTINCT de2.EventID) AS TotalEvents
    FROM DisruptionEvent de2
    JOIN ImpactsCompany ic2 ON ic2.EventID = de2.EventID
    JOIN Company c2 ON ic2.AffectedCompanyID = c2.CompanyID
    WHERE de2.EventDate <= '2025-12-31'
      AND de2.EventRecoveryDate >= '2019-01-01'
) AS total
WHERE de.EventDate <= '2025-12-31'
  AND de.EventRecoveryDate >= '2019-01-01'
AND ('2' = '' OR c.TierLevel = '2')-- Same Tier filter
GROUP BY l.CountryName, total.TotalEvents
ORDER BY RRC DESC



--RRC v2 (separated month and year, cant get all months to output, unsure about selecting continent or country)
SELECT
    CASE 
        WHEN 'Continent' = 'Continent' THEN l.ContinentName
        ELSE l.CountryName
    END AS LocationName,
    YEAR(de.EventDate) AS EventYear,
    MONTH(de.EventDate) AS EventMonth,
    COUNT(de.EventID) AS RRC_Count
FROM DisruptionEvent de
JOIN ImpactsCompany ic ON ic.EventID = de.EventID
JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
JOIN Location l ON c.LocationID = l.LocationID
WHERE
    de.EventDate BETWEEN '2025-01-01' AND '2025-06-30'
    AND ('' = '' OR c.TierLevel = '')
GROUP BY
    LocationName,
    YEAR(de.EventDate),
    MONTH(de.EventDate)
ORDER BY
    LocationName,
    EventYear,
    EventMonth;
