###############################################################################
# << anagraficaDBmeteo2_CopiaDaDBmeteo.R >>
# DESCRIZIONE
#  Trasferisci informazioni di anagrafica stazioni/sensori da DBmeteo a 
#  DBmeteo2.
#
# RIGA DI COMANDO
#  R --vanilla < anagraficaDBmeteo2_CopiaDaDBmeteo.R > anagraficaDBmeteo2_CopiaDaDBmeteo.log
#
# STORIA:
#
# data           commento
# ----           --------
#  25-gen-2011   MR e CL. codice originale + trasferimento informazioni da 
#                         DBmeteo a DBmeteo2 
#==============================================================================

#==============================================================================
# LIBRERIE - LIBRERIE - LIBRERIE - LIBRERIE - LIBRERIE - LIBRERIE - LIBRERIE  
#==============================================================================
library(DBI)
library(RMySQL)
library(RODBC)
#==============================================================================
# PARAMETRI GLOBALI - PARAMETRI GLOBALI - PARAMETRI GLOBALI - PARAMETRI GLOBALI 
#==============================================================================
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

MySQL(max.con=16,fetch.default.rec=500,force.reload=FALSE)
#definisco driver
drv<-dbDriver("MySQL")
#apro connessione con il db descritto nei parametri del gruppo "Gestione"
#nel file "/home/meteo/.my.cnf
conn<-try(dbConnect(drv,group="Visualizzazione"))
if (inherits(conn,"try-error")) {
  print( "ERRORE nell'apertura della connessione al DBmeteo \n")
  print( " Eventuale chiusura connessione malriuscita ed uscita dal programma \n")
  dbDisconnect(conn)
  rm(conn)
  dbUnloadDriver(drv)
  quit(status=1)
}

conn2<-try(dbConnect(drv,group="Gestione2"))
if (inherits(conn,"try-error")) {
  print( "ERRORE nell'apertura della connessione al DBmeteo2 \n")
  print( " Eventuale chiusura connessione malriuscita ed uscita dal programma \n")
  dbDisconnect(conn)
  rm(conn)
  dbUnloadDriver(drv)
  quit(status=1)
}
#------------------------------------------------------------------------------
# Copia A_Stazioni da DBmeteo a DBmeteo2
q_dbmeteo <- NULL
q_dbmeteo <- try(dbGetQuery(conn, "select * from A_Stazioni_new"),silent=TRUE)
if (inherits(q_dbmeteo,"try-error")) {
  print( "ERRORE nell'esecuzione query \n")
  dbDisconnect(conn)
  rm(conn)
  dbDisconnect(conn2)
  rm(conn2)
  dbUnloadDriver(drv)
  quit(status=1)
}
print(q_dbmeteo)
i<-1
while (i<=length(q_dbmeteo$IDstazione)) {
  insert_string<-paste("insert into A_Stazioni values (",q_dbmeteo$IDstazione[i],",",
                                                       "'",q_dbmeteo$NOMEstazione[i],"',",
                                                       "'',",
                                                       "'',",
                                                       q_dbmeteo$CGB_Nord[i],",",
                                                       q_dbmeteo$CGB_Est[i],",",
                                                       q_dbmeteo$lat[i],",",
                                                       q_dbmeteo$lon[i],",",
                                                       q_dbmeteo$UTM_Nord[i],",",
                                                       q_dbmeteo$UTM_Est[i],",",
                                                       q_dbmeteo$latGoogle[i],",",
                                                       q_dbmeteo$lonGoogle[i],",",
                                                       q_dbmeteo$Quota[i],",",
                                                       q_dbmeteo$IDrete[i],",",
                                                       "'",q_dbmeteo$Localita[i],"',",
                                                       "'",q_dbmeteo$Comune[i],"',",
                                                       "'",q_dbmeteo$Provincia[i],"',",
                                                       "'",q_dbmeteo$Proprieta[i],"',",
                                                       "'",q_dbmeteo$Manutenzione[i],"',",
                                                       "'",q_dbmeteo$Allerta[i],"',",
                                                       "'",q_dbmeteo$Primaria[i],"',",
                                                       "'",q_dbmeteo$DataLogger[i],"',",
                                                       "'",q_dbmeteo$Connessione[i],"',",
                                                       "'",q_dbmeteo$Alimentazione[i],"',",
                                                       "'MR',",
                                                       "'",as.character(Sys.time()),"')",sep="")
  ss<-gsub(",NA",",NULL",insert_string,fixed=TRUE)
  ss<-gsub(",'NA'",",NULL",ss,fixed=TRUE)
  ss<-gsub(",''",",NULL",ss,fixed=TRUE)
  print(insert_string)
  print(ss)
  i_dbmeteo2 <- try(dbGetQuery(conn2, ss),silent=TRUE)
  if (inherits(i_dbmeteo2,"try-error")) {
    print( "ERRORE nell'esecuzione query \n")
    dbDisconnect(conn)
    rm(conn)
    dbDisconnect(conn2)
    rm(conn2)
    dbUnloadDriver(drv)
    quit(status=1)
  }
  i<-i+1
}
#------------------------------------------------------------------------------
# Copia A_Sensori da DBmeteo a DBmeteo2
q_dbmeteo <- NULL
q_dbmeteo <- try(dbGetQuery(conn, "select * from A_Sensori_new"),silent=TRUE)
if (inherits(q_dbmeteo,"try-error")) {
  print( "ERRORE nell'esecuzione query \n")
  dbDisconnect(conn)
  rm(conn)
  dbDisconnect(conn2)
  rm(conn2)
  dbUnloadDriver(drv)
  quit(status=1)
}
print(q_dbmeteo)
i<-1
while (i<=length(q_dbmeteo$IDsensore)) {
  insert_string<-paste("insert into A_Sensori values (",q_dbmeteo$IDsensore[i],",",
                                                       q_dbmeteo$IDstazione[i],",",
                                                       "'",q_dbmeteo$NOMEtipologia[i],"',",
                                                       "'",q_dbmeteo$DataInizio[i],"',",
                                                       "'",q_dbmeteo$DataFine[i],"',",
                                                       q_dbmeteo$QuotaSensore[i],",",
                                                       "'",q_dbmeteo$NoteQS[i],"',",
                                                       "'',",
                                                       "'',",
                                                       "'',",
                                                       "'',",
                                                       "'',",
                                                       q_dbmeteo$AggregazioneTemporale[i],",",
                                                       "'MR',",
                                                       "'",as.character(Sys.time()),"')",sep="")
  ss<-gsub(",NA",",NULL",insert_string,fixed=TRUE)
  ss<-gsub(",'NA'",",NULL",ss,fixed=TRUE)
  ss<-gsub(",''",",NULL",ss,fixed=TRUE)
  print(insert_string)
  print(ss)
  i_dbmeteo2 <- try(dbGetQuery(conn2, ss),silent=TRUE)
  if (inherits(i_dbmeteo2,"try-error")) {
    print( "ERRORE nell'esecuzione query \n")
    dbDisconnect(conn)
    rm(conn)
    dbDisconnect(conn2)
    rm(conn2)
    dbUnloadDriver(drv)
    quit(status=1)
  }
  i<-i+1
}
#------------------------------------------------------------------------------
i<-1
while (i<=length(q_dbmeteo$IDsensore)) {
  insert_string<-paste("insert into A_Sensori_specifiche values (",q_dbmeteo$IDsensore[i],",",
                                                                  "'",q_dbmeteo$Marca[i],"',",
                                                                  "'",q_dbmeteo$Modello[i],"',",
                                                                  "'",q_dbmeteo$Versione[i],"',",
                                                                  "'",q_dbmeteo$Note[i],"',",
                                                                  "'',",
                                                                  "'',",
                                                                  "'MR',",
                                                                  "'",as.character(Sys.time()),"')",sep="")
  ss<-gsub(",NA",",NULL",insert_string,fixed=TRUE)
  ss<-gsub(",'NA'",",NULL",ss,fixed=TRUE)
  ss<-gsub(",''",",NULL",ss,fixed=TRUE)
  print(insert_string)
  print(ss)
  i_dbmeteo2 <- try(dbGetQuery(conn2, ss),silent=TRUE)
  if (inherits(i_dbmeteo2,"try-error")) {
    print( "ERRORE nell'esecuzione query \n")
    dbDisconnect(conn)
    rm(conn)
    dbDisconnect(conn2)
    rm(conn2)
    dbUnloadDriver(drv)
    quit(status=1)
  }
  i<-i+1
}
#------------------------------------------------------------------------------
# Uscita con successo

dbDisconnect(conn)
rm(conn)
dbDisconnect(conn2)
rm(conn2)
dbUnloadDriver(drv)


quit()

