-- Disruption Frequency over time per month with Filters

SELECT
    DATE_FORMAT(e.EventDate, '%Y-%m') AS Period,
    COUNT(*) 
        AS DisruptionCount
FROM DisruptionEvent e
WHERE e.EventDate >= '2019-01-01' AND e.EventDate <= '2025-12-31'

GROUP BY 
    DATE_FORMAT(e.EventDate, '%Y-%m')
ORDER BY 
    Period ASC;