select * from A_ListaNera where IDsensore = 17478

select count(*) as n from A_Sensori2Destinazione where IDsensore=17478 and Destinazione=14 and DataFine is not null;

select * from A_Sensori2Destinazione where IDsensore=17478 -- and Destinazione=14 and DataFine is not null;

SELECT
	IDsensore,
	s2d.Destinazione as IDdestinazione,
	d.Note as Tipo,
	d.Destinazione,
	DataInizio,
	DataFine,
	s2d.Note,
	s2d.Autore,
	s2d.Data,
	s2d.IDutente
FROM
	A_Sensori2Destinazione as s2d
		JOIN A_Destinazioni as d
			ON  s2d.Destinazione=d.IDdestinazione
WHERE
	IDsensore='17478' -- AND DataFine IS NULL 
ORDER BY Data DESC

UPDATE A_Sensori2Destinazione
SET
	IDsensore = '17478',
	Destinazione = '14',
	DataInizio = '2022-02-17 16:02:08',
	Data = '2022-02-17 16:02:59',
	IDutente = '50'
WHERE
	IDsensore = '17478' AND 
	Destinazione = '14' AND 
	DataInizio = '2022-02-17 16:02:08';

select *
from A_Reti;


select * from A_Stazioni ;

select * from A_Sensori;



								
								