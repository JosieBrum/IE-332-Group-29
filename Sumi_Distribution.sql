-- Distribution of Disruption Events
SELECT
    dc.CategoryID,
    dc.CategoryName,
    COUNT(*) * 100.0 / total.TotalEvents AS Percent
FROM ImpactsCompany ic
JOIN DisruptionEvent de ON ic.EventID = de.EventID
JOIN DisruptionCategory dc ON de.CategoryID = dc.CategoryID
JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
JOIN (
    SELECT COUNT(*) AS TotalEvents
    FROM ImpactsCompany ic
    JOIN DisruptionEvent de ON ic.EventID = de.EventID
    JOIN Company c ON ic.AffectedCompanyID = c.CompanyID
    WHERE c.CompanyName = 'Smith Inc'
      AND de.EventDate BETWEEN '2021-01-01' AND '2021-12-31'
) AS total
WHERE
    c.CompanyName = 'Smith Inc'
    AND de.EventDate BETWEEN '2021-01-01' AND '2021-12-31'
GROUP BY
    dc.CategoryID,
    dc.CategoryName,
    total.TotalEvents
ORDER BY 
	Percent DESC,
	dc.CategoryID ASC
