-- Disruption Frequency over time per month (MySQL 5.7 compatible)
SELECT
    DATE_FORMAT(DATE_ADD('2019-01-01', INTERVAL seq.seq MONTH), '%Y-%m') AS period,
    COALESCE(COUNT(DISTINCT e.EventID), 0) AS disruption_count
FROM (
    -- Generate sequence 0-83 (covers 7 years: 2019-2025 = 84 months)
    SELECT a.N + b.N * 10 AS seq
    FROM 
        (SELECT 0 AS N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 
         UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) a,
        (SELECT 0 AS N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 
         UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8) b
    ORDER BY seq
) AS seq
LEFT JOIN DisruptionEvent AS e
    ON DATE_FORMAT(e.EventDate, '%Y-%m') = DATE_FORMAT(DATE_ADD('2019-01-01', INTERVAL seq.seq MONTH), '%Y-%m')
    AND e.EventDate BETWEEN '2019-01-01' AND '2023-12-31'
LEFT JOIN ImpactsCompany AS ic 
    ON e.EventID = ic.EventID
LEFT JOIN Company AS c 
    ON ic.AffectedCompanyID = c.CompanyID
LEFT JOIN Location AS l
    ON c.LocationID = l.LocationID
WHERE
    DATE_ADD('2019-01-01', INTERVAL seq.seq MONTH) <= '2023-12-31'
    AND (e.EventID IS NULL OR ('' = '' OR l.CountryName = ''))
    AND (e.EventID IS NULL OR ('' = '' OR l.ContinentName = ''))
    AND (e.EventID IS NULL OR ('' = '' OR c.TierLevel = ''))
GROUP BY
    DATE_FORMAT(DATE_ADD('2019-01-01', INTERVAL seq.seq MONTH), '%Y-%m')
ORDER BY
    period ASC;