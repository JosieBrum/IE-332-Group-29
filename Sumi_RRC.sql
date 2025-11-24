-- RRC by Continent
SELECT
    l.ContinentName,
    COUNT(*) * 100.0 / total.TotalEvents AS RRC
FROM ImpactsCompany ic
JOIN DisruptionEvent de ON ic.EventID = de.EventID
JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
JOIN Location l ON c.LocationID = l.LocationID
JOIN (
    SELECT COUNT(*) AS TotalEvents
    FROM ImpactsCompany ic
    JOIN DisruptionEvent de ON ic.EventID = de.EventID
    JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
    JOIN Location l ON c.LocationID = l.LocationID
    WHERE de.EventDate BETWEEN '2021-01-01' AND '2021-12-31'
) AS total
WHERE
    de.EventDate BETWEEN '2021-01-01' AND '2021-12-31'
GROUP BY
    l.ContinentName,
    total.TotalEvents
ORDER BY
    RRC DESC


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
