SELECT DISTINCT A_Sensori.IDsensore FROM A_Sensori
	JOIN A_Monitoraggio ON(A_Sensori.IDsensore=A_Monitoraggio.IDsensore)
	JOIN A_Ticket ON(A_Ticket.IDticket=A_Monitoraggio.IDticket)
where A_Ticket.DataChiusura IS NULL

SELECT * FROM A_Sensori S
	LEFT JOIN A_Stazioni Z ON Z.IDstazione=S.IDstazione
		INNER JOIN A_Monitoraggio M on S.IDsensore = M.IDsensore  
			INNER JOIN A_Ticket T ON T.IDticket = M.IDticket
				-- LEFT JOIN StazioniAssegnate A ON A.IDstazione = Z.IDstazione
WHERE S.IDsensore = 12722 AND M.Chiusura = "NO"