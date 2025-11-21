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
