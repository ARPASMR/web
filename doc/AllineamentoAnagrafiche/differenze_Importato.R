#########################################################################################
# << differenze_Importato.R >>
# DESCRIZIONE
# segnala su file eventuali discrepanze tra i sensori le cui misure sono su ftp ad 
# alimentare il DBmeteo e i sensori a cui è associata la flag Importato="yes" nel DBmeteo
#
# RIGA DI COMANDO
#  R --vanilla < differenze_Importato.R > differenze_Importato.log
#
# data           autore
# ----           --------
#  19-dic-2013   MR  
#==============================================================================
#==============================================================================
# LIBRERIE - LIBRERIE - LIBRERIE - LIBRERIE - LIBRERIE - LIBRERIE - LIBRERIE  
#==============================================================================
library(DBI)
library(RMySQL)
library(RODBC)
#==============================================================================
# FUNZIONI - FUNZIONI - FUNZIONI - FUNZIONI - FUNZIONI - FUNZIONI - FUNZIONI       
#==============================================================================
#+ gestione dell'errore
neverstop<-function(){
  print("EE..ERRORE durante l'esecuzione dello script!! Messaggio d'Errore prodotto:\n")
  quit()
}
options(show.error.messages=TRUE,error=neverstop)

#==============================================================================
# MAIN - MAIN - MAIN - MAIN - MAIN - MAIN - MAIN - MAIN - MAIN - MAIN - MAIN          
#==============================================================================
print( paste(date()," > Richiedi informazioni al DBunico") )
DBunico_ch<-try(odbcConnect("odbc-sql",uid="USR",pwd="USR"))
if (inherits(DBunico_ch,"try-error")) {
  print ( "ERRORE nell'apertura della connessione al DBunico")
  print ( "Tentativo di chiusura connessione malriuscita")
  close(DBunico_ch)
  print ( "Chiusura connessione DBmeteo ed uscita dal programma")
  dbDisconnect(conn)
  rm(conn)
  dbUnloadDriver(drv)
  quit(status=1)
}
DBunico_Importati<- try( sqlQuery(DBunico_ch,paste("select IdSensore from DBCum..RichiesteAuto_Sensori where IdRichiesta = 104",sep="")))
#------------------------------------------------------------------------------
print( paste(date()," > Richiedi informazioni al DBmeteo") )
MySQL(max.con=16,fetch.default.rec=500,force.reload=FALSE)
#definisco driver
drv<-dbDriver("MySQL")
#apro connessione con il db descritto nei parametri del gruppo "Gestione"
#nel file "/home/meteo/.my.cnf
conn2<-try(dbConnect(drv,group="Visualizzazione"))
if (inherits(conn2,"try-error")) {
  print( "ERRORE nell'apertura della connessione al DBmeteo \n")
  print( " Eventuale chiusura connessione malriuscita ed uscita dal programma \n")
  dbDisconnect(conn2)
  rm(conn2)
  dbUnloadDriver(drv)
  quit(status=1)
}
DBmeteo_Importati<-try(dbGetQuery(conn2, "select IDsensore,NOMEtipologia,IDstazione  from A_Sensori where Importato='yes'"),silent=TRUE)
#
#-----------------------------------------------------------------------------
fileout<-"Importati.txt"
cat(" SEGNALAZIONI DI DISCREPANZE DBmeteo-DBunico SU QUALI SENSORI IMPORTARE \n\n",file=fileout)

#------------------------------------------------------------------------------
i<-1
    cat("sensori che nel DBmeteo risultano da importare ma non sono a disposizione dal DBunico\n\n",file=fileout,append=T)
    cat("----------------------\n",file=fileout,append=T)
    cat("IDstazione, IDsensore, tipologia\n",file=fileout,append=T)
while(i<=length(DBmeteo_Importati$IDsensore)) {
  j<-which(DBunico_Importati$IdSensore==DBmeteo_Importati$IDsensore[i])
  if (length(j)!=1) {
  DBmeteo_nomistazioni<-try(dbGetQuery(conn2, paste("select NOMEstazione from A_Stazioni where IDstazione=",DBmeteo_Importati$IDstazione[i])),silent=TRUE)
  cat(paste(DBmeteo_Importati$IDstazione[i]    ,",",
            DBmeteo_nomistazioni$NOMEstazione ,",", 
            DBmeteo_Importati$IDsensore[i]     ,",", 
            DBmeteo_Importati$NOMEtipologia[i] ,"\n"),file=fileout,append=T)
  }
i<-i+1
}
#------------------------------------------------------------------------------
    cat("\n\n**************************\n\n",file=fileout,append=T)
i<-1
    cat("sensori che risultano a disposizione dal DBunico ma nel DBmeteo non risultano da importare\n\n",file=fileout,append=T)
    cat("----------------------\n",file=fileout,append=T)
    cat("IDstazione,IDsensore (DBmeteo),IdSensore (DBunico), tipologia\n",file=fileout,append=T)
while(i<=length(DBunico_Importati$IdSensore)) {
  j<-which(DBmeteo_Importati$IDsensore==DBunico_Importati$IdSensore[i])
  if (length(j)!=1) {
  DBmeteo_nonImportati<-try(dbGetQuery(conn2,paste("select IDsensore,
					                   NOMEtipologia,
                                                           IDstazione 
                                                           from A_Sensori where IDsensore=", DBunico_Importati$IdSensore[i]) ),silent=TRUE)
  DBmeteo_stazioni<-try(dbGetQuery(conn2, paste("select NOMEstazione from A_Stazioni where IDstazione=",DBmeteo_nonImportati$IDstazione)),silent=TRUE)
 if (inherits(DBmeteo_stazioni,"try-error")) {
   cat(paste(DBmeteo_nonImportati$IDstazione  ,",",
            DBmeteo_nonImportati$IDsensore    ,",",
            DBunico_Importati$IdSensore[i]    ,",",
            DBmeteo_nonImportati$NOMEtipologia,"\n"),file=fileout,append=T)
 }else{
  cat(paste(DBmeteo_nonImportati$IDstazione   ,",", 
            DBmeteo_stazioni$NOMEstazione     ,",",
            DBmeteo_nonImportati$IDsensore    ,",",
            DBunico_Importati$IdSensore[i]    ,",", 
            DBmeteo_nonImportati$NOMEtipologia,"\n"),file=fileout,append=T)
 }
 }
i<-i+1
}
#------------------------------------------------------------------------------

close(DBunico_ch)
cat ( " \n\n\nChiusura connessione DBmeteo ed uscita dal programma con successo!!!","\n",file=fileout,append=T)
dbDisconnect(conn2)
rm(conn2)
dbUnloadDriver(drv)
warnings()
quit(status=0)

