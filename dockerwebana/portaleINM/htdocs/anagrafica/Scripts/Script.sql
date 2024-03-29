SELECT 
	A_Sensori.IDsensore, 
	A_Sensori.Aggregazione AS Aggregazione, 
	A_Sensori.IDstazione, 
	A_Sensori.NOMEtipologia, 
	A_Sensori.DataInizio, 
	A_Sensori.DataFine, 
	A_Sensori.QuotaSensore, 
	A_Sensori.QSedificio, 
	A_Sensori.QSsupporto, 
	A_Sensori.NoteQS,
	A_Sensori.Storico,
	A_Sensori.Importato,
	A_Sensori.AggregazioneTemporale,
	A_Sensori.NoteAT,
	A_Sensori.Autore,
	A_Sensori.Data, 
	A_Sensori.IDutente,
	AsText(A_Sensori.CoordUTM) as CoordUTM,
	A_Stazioni.IDstazione,
	A_Stazioni.NOMEstazione, 
	A_Stazioni.NOMEweb,
	A_Stazioni.NOMEhydstra,
	A_Stazioni.CGB_Nord,
	A_Stazioni.CGB_Est,
	A_Stazioni.lat,
	A_Stazioni.lon,
	A_Stazioni.UTM_Nord,
	A_Stazioni.UTM_Est,
	A_Stazioni.Quota,
	A_Stazioni.IDrete,
	A_Stazioni.Localita,
	A_Stazioni.Attributo,
	A_Stazioni.Comune, 
	A_Stazioni.Provincia, 
	A_Stazioni.ProprietaStazione, 
	A_Stazioni.ProprietaTerreno, 
	A_Stazioni.Manutenzione, 
	A_Stazioni.NoteManutenzione, 
	A_Stazioni.Allerta, 
	A_Stazioni.AOaib, 
	A_Stazioni.AOneve, 
	A_Stazioni.AOvalanghe, 
	A_Stazioni.LandUse, 
	A_Stazioni.PVM, 
	A_Stazioni.UrbanWeight, 
	A_Stazioni.DataLogger, 
	A_Stazioni.NoteDL, 
	A_Stazioni.Connessione,
	A_Stazioni.NoteConnessione,
	A_Stazioni.Fiduciaria,
	A_Stazioni.Alimentazione,
	A_Stazioni.NoteAlimentazione,
	A_Stazioni.Autore,
	A_Stazioni.Data, 
	A_Stazioni.IDutente, 
	AsText(A_Stazioni.CoordUTM) as CoordUTM,
	A_Stazioni.Fiume,
	A_Stazioni.Bacino,
	A_Monitoraggio.Note,
	A_Monitoraggio.DataInizio,
	A_Monitoraggio.IDticket,
	A_Ticket.DataApertura -- ,
	-- Utenti.Cognome 
FROM A_Sensori
	LEFT JOIN A_Stazioni ON A_Stazioni.IDstazione=A_Sensori.IDstazione 
		JOIN A_Monitoraggio ON A_Monitoraggio.IDsensore = A_Sensori.IDsensore
			INNER JOIN A_Ticket ON A_Ticket.IDticket = A_Monitoraggio.IDticket 
				-- LEFT JOIN StazioniAssegnate ON StazioniAssegnate.IDstazione = A_Stazioni.IDstazione 
					-- LEFT JOIN Utenti ON Utenti.IDutente = StazioniAssegnate.IDUtente
 WHERE A_Sensori.IDsensore IS NOT NULL AND A_Sensori.IDsensore IN (32278, 6459, 5910)       AND A_Monitoraggio.Chiusura = "NO"
 ORDER BY NOMEstazione


