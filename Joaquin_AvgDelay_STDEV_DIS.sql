-- AVG & STDEV of Delay in Days (by Distributor only)

SELECT 
    AVG(DATEDIFF(s.ActualDate, s.PromisedDate)) AS avg_delay_days,
    STDDEV_SAMP(DATEDIFF(s.ActualDate, s.PromisedDate)) AS std_delay_days
FROM 
    Shipping AS s
    JOIN Distributor AS d ON d.CompanyID = s.DistributorID
    JOIN Company AS c ON c.CompanyID = d.CompanyID
WHERE  
    c.CompanyName = 'Davis PLC'
    AND s.ActualDate BETWEEN '2023-01-01' AND '2023-12-31'