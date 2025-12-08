-- Average Financial Health by Company with Filters

SELECT
    c.CompanyName,
    AVG(fr.HealthScore) AS AvgFinancialHealth
FROM Company c
JOIN FinancialReport fr ON fr.CompanyID = c.CompanyID
WHERE ('Distributor' = '' OR c.Type = 'Distributor')
  AND (
        (fr.RepYear = '2019' AND fr.Quarter >= 'Q1') -- INITIAL DATE AND INITIAL QUARTER
        OR (fr.RepYear > '2019' AND fr.RepYear < '2025') -- INITIAL DATE AND END DATE
        OR (fr.RepYear = '2025' AND fr.Quarter <= 'Q4') -- END DATE AND END QUARTER
      )
GROUP BY c.CompanyID, c.CompanyName
ORDER BY AvgFinancialHealth DESC;