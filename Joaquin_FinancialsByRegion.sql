-- Company Financials by Region

SELECT
    c.CompanyName,
    fr.Quarter,
    fr.RepYear,
    fr.HealthScore
FROM Company c
JOIN FinancialReport fr 
    ON fr.CompanyID = c.CompanyID
JOIN Location l 
    ON l.LocationID = c.LocationID
WHERE ('' = '' OR l.CountryName = '')
  AND ('' = '' OR l.ContinentName = '')
  AND (
        (fr.RepYear = '2019' AND fr.Quarter >= 'Q1')
        OR (fr.RepYear > '2019' AND fr.RepYear < '2025')
        OR (fr.RepYear = '2025' AND fr.Quarter <= 'Q4')
      )
ORDER BY c.CompanyName, fr.RepYear, fr.Quarter;