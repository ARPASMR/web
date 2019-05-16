###############################################################################
# << anagraficaDBmeteo2_CopiaDaDBunico.R >>
# DESCRIZIONE
#  Trasferisci informazioni di anagrafica stazioni/sensori da DBunico a 
#  DBmeteo2.
#
# RIGA DI COMANDO
#  R --vanilla < anagraficaDBmeteo2_CopiaDaDBunico.R > anagraficaDBmeteo2_CopiaDaDBunico.log
#
# STORIA:
#
# data           commento
# ----           --------
#  26-gen-2011   MR e CL. codice originale 
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
print( paste(date()," > Richiedi informazioni al DBunico") )
DBunico_ch<-try(odbcConnect("odbc-sql",uid="USR",pwd="USR"))
if (inherits(DBunico_ch,"try-error")) {
  print ( "ERRORE nell'apertura della connessione al DBunico")
  print ( " Tentativo di chiusura connessione malriuscita")
  close(DBunico_ch)
  print ( " Chiusura connessione DBmeteo ed uscita dal programma")
  dbDisconnect(conn)
  rm(conn)
  dbUnloadDriver(drv)
  quit(status=1)
}
DBunico_Stazioni<- try( sqlQuery(DBunico_ch,paste("select * from  Stazioni where idReteVis in (1,2,4,5,6,7,8,9,10) order by NOME",sep="")))
# Campi di DBunico_Stazioni
#  1 IdStazione 
#  2 IdRete
#  3 Nome
#  4 Localita 
#  5 CGB_Nord
#  6 CGB_Est
#  7 Quota
#  8 SezioneCTR
#  9 Proprietario
# 10 IdTipoStazione
# 11 IdTipoDataLogger
# 12 IdTipoConnessione
# 13 IdTipoConnessioneBck
# 14 IdTipoRilevamento
# 15 IdStazioneOriginale
# 16 Provincia
# 17 FlagVisibile
# 18 Latitudine
# 19 Longitudine
# 20 IdAllerta
# 21 Immagine
# 22 idReteVis
# 23 idAreaIdro
# 24 idFiume
# 25 Storica
# 26 Pubblicabile
DBunico_SensStaz<- try( sqlQuery(DBunico_ch,paste("select Sensori.Descrizione,Sensori.IdSensore,Sensori.IdStazione,Stazioni.idReteVis,Stazioni.NOME,Sensori.Storica,Stazioni.Pubblicabile,Sensori.IdTipologia,Sensori.FreqAcq,DataMinimaSensorePerRaggruppamento.DataMinimaHT,DataMinimaSensorePerRaggruppamento.DataMassimaHT from  Sensori,Stazioni,DataMinimaSensorePerRaggruppamento where Sensori.IdStazione=Stazioni.IdStazione and DataMinimaSensorePerRaggruppamento.IdSensore=Sensori.IdSensore and Sensori.IdTipologia in (9,10) and Stazioni.idReteVis in (1,2,4,5,6,7,8,9,10) order by Stazioni.NOME",sep="")))
DBunico_Web_SMR_Pubblicabili<- try( sqlQuery(DBunico_ch,paste("select Nome_WEB from Web_SMR_Pubblicabili",sep="")))
DBunico_Sensori<- try( sqlQuery(DBunico_ch,paste("select * from Sensori where IdTipologia in (9,10)",sep="")))
DBunico_DataMinimaSensorePerRaggruppamento<- try( sqlQuery(DBunico_ch,paste("select * from DataMinimaSensorePerRaggruppamento",sep="")))
DBunico_SensoriDettagli<- try( sqlQuery(DBunico_ch,paste("select * from SensoriDettagli",sep="")))
DBunico_ReportStazioni<- try( sqlQuery(DBunico_ch,paste("select * from ReportStazioni",sep="")))

#str<-"   Now is the time      "
## Elimina spazi bianchi prima della stringa
#sub(' +', '', str)  ## spaces only
#[1] "Now is the time      "
## Elimina spazi bianchi dopo la stringa
#sub(' +$', '', str)  ## spaces only
#"   Now is the time"
DBunico_Stazioni$IdAllerta<-sub(' +', '', DBunico_Stazioni$IdAllerta)
DBunico_Stazioni$IdAllerta<-sub(' +$', '', DBunico_Stazioni$IdAllerta)
#------------------------------------------------------------------------------
print( paste(date()," > Richiedi informazioni al DBmeteo2") )
MySQL(max.con=16,fetch.default.rec=500,force.reload=FALSE)
#definisco driver
drv<-dbDriver("MySQL")
#apro connessione con il db descritto nei parametri del gruppo "Gestione"
#nel file "/home/meteo/.my.cnf
conn2<-try(dbConnect(drv,group="Visualizzazione"))
if (inherits(conn2,"try-error")) {
  print( "ERRORE nell'apertura della connessione al DBmeteo2 \n")
  print( " Eventuale chiusura connessione malriuscita ed uscita dal programma \n")
  dbDisconnect(conn2)
  rm(conn2)
  dbUnloadDriver(drv)
  quit(status=1)
}
DBmeteo2_Stazioni<-try(dbGetQuery(conn2, "select * from A_Stazioni"),silent=TRUE)
DBmeteo2_Sensori<-try(dbGetQuery(conn2, "select * from A_Sensori"),silent=TRUE)
DBmeteo2_Tipologia<-try(dbGetQuery(conn2, "select * from A_Tipologia"),silent=TRUE)
# rimuovi spazi bianchi che precedono e che seguono la stringa
#DBmeteo2_Stazioni$NOMEstazione<-sub(' +', '', DBmeteo2_Stazioni$NOMEstazione)
DBmeteo2_Stazioni$NOMEstazione<-sub(' +$', '', DBmeteo2_Stazioni$NOMEstazione)
DBmeteo2_Stazioni$Allerta<-sub(' +', '', DBmeteo2_Stazioni$Allerta)
DBmeteo2_Stazioni$Allerta<-sub(' +$', '', DBmeteo2_Stazioni$Allerta)
#------------------------------------------------------------------------------
fileout<-"checkGENERICI.txt"

fileout_NOMI<-"checkNOMIstaz.txt"
fileout_COORD<-"checkCOORDstaz.txt"
fileout_QUOTA<-"checkQUOTAstaz.txt"
fileout_RETE<-"checkRETEstaz.txt"
fileout_PROV<-"checkPROVstaz.txt"
fileout_ALLERTA<-"checkALLERTAstaz.txt"

fileout_TIP<-"checkTIPOLOGIAsens.txt"
fileout_STORICO<-"checkSTORICOsens.txt"
fileout_FIDUCIARIA<-"checkFIDUCIARIAsens.txt"
fileout_WEB<-"checkWEBsens.txt"
fileout_GOOGLE<-"checkGOOGLEsens.txt"
fileout_AGG<-"checkAGGsens.txt"
fileout_DATAI<-"checkDATAIsens.txt"
#fileout_DATAF<-"checkDATAFsens.txt"

cat(" ESITO DEI CHECK GENERICI\n\n",file=fileout)
cat(" ESITO DEI CHECK sui NOMI \n\n",file=fileout_NOMI)
cat(" ESITO DEI CHECK sulle COORDINATE \n\n",file=fileout_COORD)
cat(" ESITO DEI CHECK sulle QUOTA \n\n",file=fileout_QUOTA)
cat(" ESITO DEI CHECK sulle RETE \n\n",file=fileout_RETE)
cat(" ESITO DEI CHECK sulla PROVINCIA \n\n",file=fileout_PROV)
cat(" ESITO DEI CHECK sulla ALLERTA \n\n",file=fileout_ALLERTA)

cat(" ESITO DEI CHECK sulla TIPOLOGIA \n\n",file=fileout_TIP)
cat(" ESITO DEI CHECK sulla STORICO \n\n",file=fileout_STORICO)
cat(" ESITO DEI CHECK sulla FIDUCIARIA \n\n",file=fileout_FIDUCIARIA)
cat(" ESITO DEI CHECK sulla WEB \n\n",file=fileout_WEB)
cat(" ESITO DEI CHECK sulla AGGREGAZIONE TEMPORALE \n\n",file=fileout_AGG)
cat(" ESITO DEI CHECK sulla GOOGLE \n\n",file=fileout_GOOGLE)
cat(" ESITO DEI CHECK sulla DATA INIZIO \n\n",file=fileout_DATAI)
#cat(" ESITO DEI CHECK sulla DATA FINE \n\n",file=fileout_DATAF)

# + Controlli di consistenza base
cat("\nRicerca stazioni appartenenti al DBunico E ad una rete di interesse E
       aventi almeno un sensore di interesse MA senza quota\n",file=fileout,append=T)
aux<-is.na(DBunico_Stazioni$Quota)
aux1<-DBunico_Stazioni$IdStazione[aux] %in% DBunico_SensStaz$IdStazione
cat( rbind( as.vector(DBunico_Stazioni$Quota[aux][aux1]),
              as.vector(DBunico_Stazioni$Nome[aux][aux1]),"\n" ),"\n" ,file=fileout,append=T)

cat("\nRicerca stazioni appartenenti al DBunico E ad una rete di interesse E
       aventi almeno un sensore di interesse MA senza CGB_Nord\n",file=fileout,append=T)
aux<-is.na(DBunico_Stazioni$CGB_Nord)
aux1<-DBunico_Stazioni$IdStazione[aux] %in% DBunico_SensStaz$IdStazione
cat( rbind( as.vector(DBunico_Stazioni$CGB_Nord[aux][aux1]),
              as.vector(DBunico_Stazioni$Nome[aux][aux1]),"\n" ) ,"\n",file=fileout,append=T)

cat("\nRicerca stazioni appartenenti al DBunico E ad una rete di interesse E
       aventi almeno un sensore di interesse MA senza CGB_Est\n",file=fileout,append=T)
aux<-is.na(DBunico_Stazioni$CGB_Est)
aux1<-DBunico_Stazioni$IdStazione[aux] %in% DBunico_SensStaz$IdStazione
cat( rbind( as.vector(DBunico_Stazioni$CGB_Est[aux][aux1]),
              as.vector(DBunico_Stazioni$Nome[aux][aux1]) ,"\n"),"\n" ,file=fileout,append=T)

cat("\nRicerca stazioni appartenenti al DBunico E ad una rete di interesse E
       aventi almeno un sensore di interesse MA senza Nome\n",file=fileout,append=T)
aux<-is.na(DBunico_Stazioni$Nome)
aux1<-DBunico_Stazioni$IdStazione[aux] %in% DBunico_SensStaz$IdStazione
cat( rbind( as.vector(DBunico_Stazioni$IdStazione[aux][aux1]),
              as.vector(DBunico_Stazioni$Nome[aux][aux1]),"\n" ),"\n",file=fileout,append=T )

cat("\nRicerca stazioni appartenenti al DBunico E ad una rete di interesse E
       aventi almeno un sensore di interesse MA senza Provincia\n",file=fileout,append=T)
aux<-is.na(DBunico_Stazioni$Provincia)
aux1<-DBunico_Stazioni$IdStazione[aux] %in% DBunico_SensStaz$IdStazione
cat( rbind( as.vector(DBunico_Stazioni$Provincia[aux][aux1]),
              as.vector(DBunico_Stazioni$Nome[aux][aux1]),"\n" ),"\n",file=fileout,append=T )

cat("\nRicerca stazioni lombarde e appartenenti al DBunico E ad una rete di
       interesse E aventi almeno un sensore di interesse MA senza IDallerta\n",file=fileout,append=T)
aux<-is.na(DBunico_Stazioni$IdAllerta) & (DBunico_Stazioni$Provincia=="BG" |
     DBunico_Stazioni$Provincia=="BS" | DBunico_Stazioni$Provincia=="CO" |
     DBunico_Stazioni$Provincia=="CR" | DBunico_Stazioni$Provincia=="LC" |
     DBunico_Stazioni$Provincia=="LO" | DBunico_Stazioni$Provincia=="MB" |
     DBunico_Stazioni$Provincia=="MI" | DBunico_Stazioni$Provincia=="MN" |
     DBunico_Stazioni$Provincia=="PV" | DBunico_Stazioni$Provincia=="SO" |
     DBunico_Stazioni$Provincia=="VA")
aux1<-DBunico_Stazioni$IdStazione[aux] %in% DBunico_SensStaz$IdStazione
cat( rbind( as.vector(DBunico_Stazioni$IdAllerta[aux][aux1]),
              as.vector(DBunico_Stazioni$Provincia[aux][aux1]),
              as.vector(DBunico_Stazioni$Nome[aux][aux1]),"\n" ) ,"\n",file=fileout,append=T)

cat("\nRicerca stazioni appartenenti al DBunico  E ad una rete di interesse E
       aventi almeno un sensore di interesse MA non appartenenti al DBmeteo2\n",file=fileout,append=T)
aux<-!(DBunico_Stazioni$IdStazione %in% DBmeteo2_Stazioni$IDstazione)
aux1<-DBunico_Stazioni$IdStazione[aux] %in% DBunico_SensStaz$IdStazione
cat( rbind( as.vector(DBunico_Stazioni$IdStazione[aux][aux1]),
              as.vector(DBunico_Stazioni$Provincia[aux][aux1]),
              as.vector(DBunico_Stazioni$Nome[aux][aux1]) ,"\n") ,"\n",file=fileout,append=T)

cat("\nRicerca sensori appartenenti al DBunico  E ad una rete di interesse E
       di interesse MA non appartenenti al DBmeteo2\n",file=fileout,append=T)
aux1<-!(DBunico_SensStaz$IdSensore %in% DBmeteo2_Sensori$IDsensore)
print(DBunico_SensStaz$IdSensore[aux1])
print(DBunico_SensStaz$Descrizione[aux1])
print(DBunico_SensStaz$IdStazione[aux1])
print(DBunico_SensStaz$NOME[aux1])
print(DBunico_SensStaz$IdTipologia[aux1])
print(DBunico_SensStaz$idReteVis[aux1])
#segnalazione <-data.frame(DBunico_SensStaz$IdSensore[aux1],
#	DBunico_SensStaz$Descrizione[aux1],
#	DBunico_SensStaz$IdStazione[aux1],
#	DBunico_SensStaz$NOME[aux1],
#	DBunico_SensStaz$IdTipologia[aux1],
#	DBunico_SensStaz$idReteVis[aux1])



##maria
if (length(DBunico_SensStaz$IdSensore[aux1])>0) {
#   cat(segnalazione, file=fileout
  cat(rbind(as.vector(DBunico_SensStaz$IdSensore[aux1]),
              ",",
              as.vector(DBunico_SensStaz$IdStazione[aux1]),
              ",",
              as.vector(DBunico_SensStaz$NOME[aux1]),
              ",",
              as.vector(DBunico_SensStaz$IdTipologia[aux1]),
              ",",
              as.vector(DBunico_SensStaz$Descrizione[aux1]),
              ",",
              as.vector(DBunico_SensStaz$idReteVis[aux1])  ,"\n") ,"\n",file=fileout,append=T)
} else {
  cat("\nSensori trovate 0","\n",file=fileout,append=T)
}

cat("\nRicerca stazioni appartenenti al DBmeteo2 ma non appartenenti al DBunico\n",file=fileout,append=T)
aux<-DBmeteo2_Stazioni$IDstazione %in% DBunico_Stazioni$IdStazione
if (length(DBmeteo2_Stazioni$NOMEstazione[!aux])>0) {
  cat(DBmeteo2_Stazioni$NOMEstazione[!aux],"\n",file=fileout,append=T)
} else {
  cat("\nStazioni trovate 0\n",file=fileout,append=T)
}

cat("\nRicerca sensori appartenenti al DBmeteo2 ma non appartenenti al DBunico\n",file=fileout,append=T)
aux<-DBmeteo2_Sensori$IDsensore %in% DBunico_Sensori$IdSensore
if (length(DBmeteo2_Sensori$IDsensore[!aux])>0) {
  cat(DBmeteo2_Sensori$IDsensore[!aux],"\n",file=fileout,append=T)
} else {
  cat("\nSensori trovate 0\n",file=fileout,append=T)
}
#------------------------------------------------------------------------------
# + Fra le info nel DBmeteo2: segnala parametri che variano rispetto al DBunico 
# Stazioni
cat("\n\n\n<<<<<<<<<<<<<<<<<<<<<<<<<<Fra le info nel DBmeteo2: segnala parametri che variano rispetto al DBunico [Stazioni]\n",file=fileout,append=T)
i<-1
while(i<=length(DBmeteo2_Stazioni$IDstazione)) {
  j<-which(DBunico_Stazioni$IdStazione==DBmeteo2_Stazioni$IDstazione[i])
  if (length(j)!=1) {
  cat("-----------------------------------------------------------------\n",file=fileout,append=T)
  cat(paste(i,". ID stazione =",DBmeteo2_Stazioni$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[i]),"\n",file=fileout,append=T)
    cat("l'ID stazione esiste nel DB METEO2 ma non nel DBunico\n",file=fileout,append=T)
    cat(paste("ID stazione =",DBmeteo2_Stazioni$IDstazione[i]),"\n",file=fileout,append=T)
  } else {
#
    if ( is.na(DBmeteo2_Stazioni$NOMEstazione[i]) | is.na(DBunico_Stazioni$Nome[j]) ) {
      if ( is.na(DBmeteo2_Stazioni$NOMEstazione[i]) & is.na(DBunico_Stazioni$Nome[j]) ) {
  #      cat("NOMEstazione...OK\n",file=fileout,append=T)
      } else {
  cat("-----------------------------------------------------------------\n",file=fileout_NOMI,append=T)
  cat(paste(i,". ID stazione =",DBmeteo2_Stazioni$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[i]),"\n",file=fileout_NOMI,append=T)
    #    cat("@@@@@@@@@@@@@@@ NOMEstazione variato!\n",file=fileout_NOMI,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Stazioni$NOMEstazione[i]),"\n",file=fileout_NOMI,append=T)
        cat(paste(" DBunico: ",DBunico_Stazioni$Nome[j]),"\n",file=fileout_NOMI,append=T)
      }
    } else {
      if (DBmeteo2_Stazioni$NOMEstazione[i]==DBunico_Stazioni$Nome[j]){
  #      cat("NOMEstazione...OK\n",file=fileout,append=T)
      } else {
  cat("-----------------------------------------------------------------\n",file=fileout_NOMI,append=T)
  cat(paste(i,". ID stazione =",DBmeteo2_Stazioni$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[i]),"\n",file=fileout_NOMI,append=T)
  #      cat("@@@@@@@@@@@@@@@ NOMEstazione variato!\n",file=fileout_NOMI,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Stazioni$NOMEstazione[i]),"\n",file=fileout_NOMI,append=T)
        cat(paste(" DBunico: ",DBunico_Stazioni$Nome[j]),"\n",file=fileout_NOMI,append=T)
      }
    }

#
#    if ( is.na(DBmeteo2_Stazioni$NOMEweb[i]) | is.na(DBunico_Stazioni$Nome_WEB[j]) ) {
#      if ( is.na(DBmeteo2_Stazioni$NOMEweb[i]) & is.na(DBunico_Stazioni$Nome_WEB[j]) ) {
#        print("NOMEweb...OK")
#      } else {
#        print("@@@@@@@@@@@@@@@ NOMEweb variato!")
#        print(paste("DBmeteo2: ",DBmeteo2_Stazioni$NOMEweb[i]))
#        print(paste(" DBunico: ",DBunico_Stazioni$Nome_WEB[j]))
#      }
#    } else {
#      if (DBmeteo2_Stazioni$NOMEweb[i]==DBunico_Stazioni$Nome_WEB[j]){
#        print("NOMEweb...OK")
#      } else {
#        print("@@@@@@@@@@@@@@@ NOMEweb variato!")
#        print(paste("DBmeteo2: ",DBmeteo2_Stazioni$NOMEweb[i]))
#        print(paste(" DBunico: ",DBunico_Stazioni$Nome_WEB[j]))
#      }
#    }
#
    if ( is.na(DBmeteo2_Stazioni$CGB_Nord[i]) | is.na(DBunico_Stazioni$CGB_Nord[j]) ) {
      if ( is.na(DBmeteo2_Stazioni$CGB_Nord[i]) & is.na(DBunico_Stazioni$CGB_Nord[j]) ) {
  #      cat("CGB_Nord...OK\n",file=fileout,append=T)
      } else {
  cat("-----------------------------------------------------------------\n",file=fileout_COORD,append=T)
  cat(paste(i,". ID stazione =",DBmeteo2_Stazioni$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[i]),"\n",file=fileout_COORD,append=T)
        cat("@@@@@@@@@@@@@@@ CGB_Nord variato!\n",file=fileout_COORD,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Stazioni$CGB_Nord[i]),"\n",file=fileout_COORD,append=T)
        cat(paste(" DBunico: ",DBunico_Stazioni$CGB_Nord[j]),"\n",file=fileout_COORD,append=T)
      }
    } else {
      if (DBmeteo2_Stazioni$CGB_Nord[i]==DBunico_Stazioni$CGB_Nord[j]){
  #      cat("CGB_Nord...OK\n",file=fileout,append=T)
      } else {
  cat("-----------------------------------------------------------------\n",file=fileout_COORD,append=T)
  cat(paste(i,". ID stazione =",DBmeteo2_Stazioni$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[i]),"\n",file=fileout_COORD,append=T)
        cat("@@@@@@@@@@@@@@@ CGB_Nord variato!\n","\n",file=fileout_COORD,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Stazioni$CGB_Nord[i]),"\n",file=fileout_COORD,append=T)
        cat(paste(" DBunico: ",DBunico_Stazioni$CGB_Nord[j]),"\n",file=fileout_COORD,append=T)
      }
    }
#
    if ( is.na(DBmeteo2_Stazioni$CGB_Est[i]) | is.na(DBunico_Stazioni$CGB_Est[j]) ) {
      if ( is.na(DBmeteo2_Stazioni$CGB_Est[i]) & is.na(DBunico_Stazioni$CGB_Est[j]) ) {
  #      cat("CGB_Est...OK\n",file=fileout,append=T)
      } else {
  cat("-----------------------------------------------------------------\n",file=fileout_COORD,append=T)
  cat(paste(i,". ID stazione =",DBmeteo2_Stazioni$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[i]),"\n",file=fileout_COORD,append=T)
        cat("@@@@@@@@@@@@@@@ CGB_Est variato!\n","\n",file=fileout_COORD,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Stazioni$CGB_Est[i]),"\n",file=fileout_COORD,append=T)
        cat(paste(" DBunico: ",DBunico_Stazioni$CGB_Est[j]),"\n",file=fileout_COORD,append=T)
      }
    } else {
      if (DBmeteo2_Stazioni$CGB_Est[i]==DBunico_Stazioni$CGB_Est[j]){
       #cat("CGB_Est...OK\n",file=fileout,append=T)
      } else {
  cat("-----------------------------------------------------------------\n",file=fileout_COORD,append=T)
  cat(paste(i,". ID stazione =",DBmeteo2_Stazioni$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[i]),"\n",file=fileout_COORD,append=T)
        cat("@@@@@@@@@@@@@@@ CGB_Est variato!\n",file=fileout_COORD,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Stazioni$CGB_Est[i]),"\n",file=fileout_COORD,append=T)
        cat(paste(" DBunico: ",DBunico_Stazioni$CGB_Est[j]),"\n",file=fileout_COORD,append=T)
      }
    }
#
    if ( is.na(DBmeteo2_Stazioni$Quota[i]) | is.na(DBunico_Stazioni$Quota[j]) ) {
      if ( is.na(DBmeteo2_Stazioni$Quota[i]) & is.na(DBunico_Stazioni$Quota[j]) ) {
  #      cat("Quota...OK\n",file=fileout,append=T)
      } else {
  cat("-----------------------------------------------------------------\n",file=fileout_QUOTA,append=T)
  cat(paste(i,". ID stazione =",DBmeteo2_Stazioni$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[i]),"\n",file=fileout_QUOTA,append=T)
  #      cat("@@@@@@@@@@@@@@@ Quota variato!\n",file=fileout_QUOTA,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Stazioni$Quota[i]),"\n",file=fileout_QUOTA,append=T)
        cat(paste(" DBunico: ",DBunico_Stazioni$Quota[j]),"\n",file=fileout_QUOTA,append=T)
      }
    } else {
      if (DBmeteo2_Stazioni$Quota[i]==DBunico_Stazioni$Quota[j]){
  #      cat("Quota...OK\n",file=fileout,append=T)
      } else {
  cat("-----------------------------------------------------------------\n",file=fileout_QUOTA,append=T)
  cat(paste(i,". ID stazione =",DBmeteo2_Stazioni$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[i]),"\n",file=fileout_QUOTA,append=T)
  #      cat("@@@@@@@@@@@@@@@ Quota variato!\n",file=fileout_QUOTA,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Stazioni$Quota[i]),"\n",file=fileout_QUOTA,append=T)
        cat(paste(" DBunico: ",DBunico_Stazioni$Quota[j]),"\n",file=fileout_QUOTA,append=T)
      }
    }
#
    if ( is.na(DBmeteo2_Stazioni$IDrete[i]) | is.na(DBunico_Stazioni$idReteVis[j]) ) {
      if ( is.na(DBmeteo2_Stazioni$IDrete[i]) & is.na(DBunico_Stazioni$idReteVis[j]) ) {
  #      cat("IDrete...OK\n",file=fileout,append=T)
      } else {
  cat("-----------------------------------------------------------------\n",file=fileout_RETE,append=T)
  cat(paste(i,". ID stazione =",DBmeteo2_Stazioni$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[i]),"\n",file=fileout_RETE,append=T)
  #      cat("@@@@@@@@@@@@@@@ IDrete variato!\n",file=fileout_RETE,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Stazioni$IDrete[i]),"\n",file=fileout_RETE,append=T)
        cat(paste(" DBunico: ",DBunico_Stazioni$idReteVis[j]),"\n",file=fileout_RETE,append=T)
      }
    } else {
      if (DBmeteo2_Stazioni$IDrete[i]==DBunico_Stazioni$idReteVis[j]){
  #      cat("Quota...OK\n",file=fileout,append=T)
      } else {
  cat("-----------------------------------------------------------------\n",file=fileout_RETE,append=T)
  cat(paste(i,". ID stazione =",DBmeteo2_Stazioni$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[i]),"\n",file=fileout_RETE,append=T)
  #      cat("@@@@@@@@@@@@@@@ IDrete variato!\n",file=fileout_RETE,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Stazioni$IDrete[i]),"\n",file=fileout_RETE,append=T)
        cat(paste(" DBunico: ",DBunico_Stazioni$idReteVis[j]),"\n",file=fileout_RETE,append=T)
      }
    }
#
    if ( is.na(DBmeteo2_Stazioni$Provincia[i]) | is.na(DBunico_Stazioni$Provincia[j]) ) {
      if ( is.na(DBmeteo2_Stazioni$Provincia[i]) & is.na(DBunico_Stazioni$Provincia[j]) ) {
  #      cat("Provincia...OK\n",file=fileout,append=T)
      } else {
  cat("-----------------------------------------------------------------\n",file=fileout_PROV,append=T)
  cat(paste(i,". ID stazione =",DBmeteo2_Stazioni$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[i]),"\n",file=fileout_PROV,append=T)
  #      cat("@@@@@@@@@@@@@@@ Provincia variato!\n",file=fileout_PROV,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Stazioni$Provincia[i]),"\n",file=fileout_PROV,append=T)
        cat(paste(" DBunico: ",DBunico_Stazioni$Provincia[j]),"\n",file=fileout_PROV,append=T)
      }
    } else {
      if (DBmeteo2_Stazioni$Provincia[i]==DBunico_Stazioni$Provincia[j]){
  #      cat("Provincia..OK\n",file=fileout,append=T)
      } else {
  cat("-----------------------------------------------------------------\n",file=fileout_PROV,append=T)
  cat(paste(i,". ID stazione =",DBmeteo2_Stazioni$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[i]),"\n",file=fileout_PROV,append=T)
  #      cat("@@@@@@@@@@@@@@@ Provincia variato!\n",file=fileout_PROV,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Stazioni$Provincia[i]),"\n",file=fileout_PROV,append=T)
        cat(paste(" DBunico: ",DBunico_Stazioni$Provincia[j]),"\n",file=fileout_PROV,append=T)
      }
    }
#
    if ( is.na(DBmeteo2_Stazioni$Allerta[i]) | is.na(DBunico_Stazioni$IdAllerta[j]) ) {
      if ( is.na(DBmeteo2_Stazioni$Allerta[i]) & is.na(DBunico_Stazioni$IdAllerta[j]) ) {
  #      cat("Allerta...OK\n",file=fileout,append=T)
      } else {
  cat("-----------------------------------------------------------------\n",file=fileout_ALLERTA,append=T)
  cat(paste(i,". ID stazione =",DBmeteo2_Stazioni$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[i]),"\n",file=fileout_ALLERTA,append=T)
  #      cat("@@@@@@@@@@@@@@@ Allerta variato!\n",file=fileout_ALLERTA,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Stazioni$Allerta[i]),"\n",file=fileout_ALLERTA,append=T)
        cat(paste(" DBunico: ",DBunico_Stazioni$IdAllerta[j]),"\n",file=fileout_ALLERTA,append=T)
      }
    } else {
      if (DBmeteo2_Stazioni$Allerta[i]==DBunico_Stazioni$IdAllerta[j]){
  #      cat("Allerta..OK\n",file=fileout,append=T)
      } else {
  cat("-----------------------------------------------------------------\n",file=fileout_ALLERTA,append=T)
  cat(paste(i,". ID stazione =",DBmeteo2_Stazioni$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[i]),"\n",file=fileout_ALLERTA,append=T)
  #      cat("@@@@@@@@@@@@@@@ Allerta variato!\n",file=fileout_ALLERTA,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Stazioni$Allerta[i]),"\n",file=fileout_ALLERTA,append=T)
        cat(paste(" DBunico: ",DBunico_Stazioni$IdAllerta[j]),"\n",file=fileout_ALLERTA,append=T)
      }
    }
#
  }
  i<-i+1
} 
# Sensori
#DBunico_SensStaz<- try( sqlQuery(DBunico_ch,paste("select Sensori.IdSensore,Sensori.IdStazione,Stazioni.idReteVis,Stazioni.NOME from  Sensori,Stazioni where Sensori.IdStazione=Stazioni.IdStazione and Sensori.IdTipologia in (2,3,5,9,10,11,12,13) and Stazioni.idReteVis in (1,2,4,5,6,7,8,9,10) order by Stazioni.NOME",sep="")))
cat("\n\n\n<<<<<<<<<<<<<<<<<<<<Fra le info nel DBmeteo2: segnala parametri che variano rispetto al DBunico [Sensori]","\n",file=fileout,append=T)
i<-1
while(i<=length(DBmeteo2_Sensori$IDsensore)) {
  j<-which(DBunico_SensStaz$IdSensore==DBmeteo2_Sensori$IDsensore[i])
  if (length(j)!=1) {
  cat("-----------------------------------------------------------------","\n",file=fileout,append=T)
  cat(paste(i,". ID sensore =",DBmeteo2_Sensori$IDsensore[i],DBmeteo2_Sensori$NOMEtipologia[i],DBmeteo2_Sensori$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[DBmeteo2_Stazioni$IDstazione==DBmeteo2_Sensori$IDstazione[i]]),"\n",file=fileout,append=T)
    cat("l'ID sensore esiste nel DB METEO2 ma non nel DBunico","\n",file=fileout,append=T)
   # cat(paste("ID sensore =",DBmeteo2_Sensori$IDsensore[i]),"\n",file=fileout,append=T)
  } else {
#
    if ( is.na(DBmeteo2_Sensori$IDstazione[i]) | is.na(DBunico_SensStaz$IdStazione[j]) ) {
      if ( is.na(DBmeteo2_Sensori$IDstazione[i]) & is.na(DBunico_SensStaz$IdStazione[j]) ) {
       # print("IDstazione...OK")
      } else {
  cat("-----------------------------------------------------------------","\n",file=fileout,append=T)
  cat(paste(i,". ID sensore =",DBmeteo2_Sensori$IDsensore[i],DBmeteo2_Sensori$NOMEtipologia[i],DBmeteo2_Sensori$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[DBmeteo2_Stazioni$IDstazione==DBmeteo2_Sensori$IDstazione[i]]),"\n",file=fileout,append=T)
        cat("@@@@@@@@@@@@@@@ IDstazione variato!","\n",file=fileout,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Sensori$IDstazione[i]),"\n",file=fileout,append=T)
        cat(paste(" DBunico: ",DBunico_SensStaz$IdStazione[j]),"\n",file=fileout,append=T)
      }
    } else {
      if (DBmeteo2_Sensori$IDstazione[i]==DBunico_SensStaz$IdStazione[j]){
       # print("IDstazione..OK")
      } else {
  cat("-----------------------------------------------------------------","\n",file=fileout,append=T)
  cat(paste(i,". ID sensore =",DBmeteo2_Sensori$IDsensore[i],DBmeteo2_Sensori$NOMEtipologia[i],DBmeteo2_Sensori$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[DBmeteo2_Stazioni$IDstazione==DBmeteo2_Sensori$IDstazione[i]]),"\n",file=fileout,append=T)
        cat("@@@@@@@@@@@@@@@ IDstazione variato!","\n",file=fileout,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Sensori$IDstazione[i]),"\n",file=fileout,append=T)
        cat(paste(" DBunico: ",DBunico_SensStaz$IdStazione[j]),"\n",file=fileout,append=T)
      }
    }
#
    aux<-DBmeteo2_Tipologia$IdTipologia[DBmeteo2_Tipologia$Nome==DBmeteo2_Sensori$NOMEtipologia[i]]
#    print(DBmeteo2_Tipologia)
#    print(DBmeteo2_Sensori$NOMEtipologia[i])
#    print(aux)
#    print(DBunico_SensStaz$IdTipologia[j])
    if ( is.na(aux) | is.na(DBunico_SensStaz$IdTipologia[j]) ) {
      if ( is.na(aux) & is.na(DBunico_SensStaz$IdTipologia[j]) ) {
       # print("IDtipologia...OK")
      } else {
  cat("-----------------------------------------------------------------","\n",file=fileout_TIP,append=T)
  cat(paste(i,". ID sensore =",DBmeteo2_Sensori$IDsensore[i],DBmeteo2_Sensori$NOMEtipologia[i],DBmeteo2_Sensori$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[DBmeteo2_Stazioni$IDstazione==DBmeteo2_Sensori$IDstazione[i]]),"\n",file=fileout_TIP,append=T)
  #      cat("@@@@@@@@@@@@@@@ IDtipologia variato!","\n",file=fileout_TIP,append=T)
        cat(paste("DBmeteo2: ",aux),"\n",file=fileout_TIP,append=T)
        cat(paste(" DBunico: ",DBunico_SensStaz$IdTipologia[j]),"\n",file=fileout_TIP,append=T)
      }
    } else {
      if (aux==DBunico_SensStaz$IdTipologia[j]){
       # print("IDtipologia...OK")
      } else {
  cat("-----------------------------------------------------------------","\n",file=fileout_TIP,append=T)
  cat(paste(i,". ID sensore =",DBmeteo2_Sensori$IDsensore[i],DBmeteo2_Sensori$NOMEtipologia[i],DBmeteo2_Sensori$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[DBmeteo2_Stazioni$IDstazione==DBmeteo2_Sensori$IDstazione[i]]),"\n",file=fileout_TIP,append=T)
  #      cat("@@@@@@@@@@@@@@@ IDtipologia variato!","\n",file=fileout_TIP,append=T)
        cat(paste("DBmeteo2: ",aux),"\n",file=fileout_TIP,append=T)
        cat(paste(" DBunico: ",DBunico_SensStaz$IdTipologia[j]),"\n",file=fileout_TIP,append=T)
      }
    }
#
    if (is.na(DBunico_SensStaz$Storica[j])) {
      aux1<-NA
    } else if (DBunico_SensStaz$Storica[j]=='S') {
      aux1<-'Yes'
    } else if (DBunico_SensStaz$Storica[j]=='N') {
      aux1<-'No'
    }
    if ( is.na(DBmeteo2_Sensori$Storico[i]) | is.na(aux1) ) {
      if ( is.na(DBmeteo2_Sensori$Storico[i]) & is.na(aux1) ) {
       # print("Storico...OK")
      } else {
  cat("-----------------------------------------------------------------","\n",file=fileout_STORICO,append=T)
  cat(paste(i,". ID sensore =",DBmeteo2_Sensori$IDsensore[i],DBmeteo2_Sensori$NOMEtipologia[i],DBmeteo2_Sensori$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[DBmeteo2_Stazioni$IDstazione==DBmeteo2_Sensori$IDstazione[i]]),"\n",file=fileout_STORICO,append=T)
  #      cat("@@@@@@@@@@@@@@@ Storico variato!","\n",file=fileout_STORICO,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Sensori$Storico[i]),"\n",file=fileout_STORICO,append=T)
        cat(paste(" DBunico: ",aux1),"\n",file=fileout_STORICO,append=T)
      }
    } else {
      if (DBmeteo2_Sensori$Storico[i]==aux1){
       # print("Storico..OK")
      } else {
  cat("-----------------------------------------------------------------","\n",file=fileout_STORICO,append=T)
  cat(paste(i,". ID sensore =",DBmeteo2_Sensori$IDsensore[i],DBmeteo2_Sensori$NOMEtipologia[i],DBmeteo2_Sensori$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[DBmeteo2_Stazioni$IDstazione==DBmeteo2_Sensori$IDstazione[i]]),"\n",file=fileout_STORICO,append=T)
  #      cat("@@@@@@@@@@@@@@@ Storico variato!","\n",file=fileout_STORICO,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Sensori$Storico[i]),"\n",file=fileout_STORICO,append=T)
        cat(paste(" DBunico: ",aux1),"\n",file=fileout_STORICO,append=T)
      }
    }
#
    aux<-grepl("\\*",toString(DBunico_SensStaz$NOME[j]))
    if (aux) {
      aux1<-'Yes'
    } else {
      aux1<-'No'
    }
##    if ( is.na(DBmeteo2_Sensori$Fiduciaria[i]) | is.na(aux1) ) {
##      if ( is.na(DBmeteo2_Sensori$Fiduciaria[i]) & is.na(aux1) ) {
##       # print("Fiduciaria...OK")
##      } else {
##  cat("-----------------------------------------------------------------","\n",file=fileout_FIDUCIARIA,append=T)
##  cat(paste(i,". ID sensore =",DBmeteo2_Sensori$IDsensore[i],DBmeteo2_Sensori$NOMEtipologia[i],DBmeteo2_Sensori$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[DBmeteo2_Stazioni$IDstazione==DBmeteo2_Sensori$IDstazione[i]]),"\n",file=fileout_FIDUCIARIA,append=T)
##  #      cat("@@@@@@@@@@@@@@@ Fiduciaria variato!","\n",file=fileout_FIDUCIARIA,append=T)
##        cat(paste("DBmeteo2: ",DBmeteo2_Sensori$Fiduciaria[i]),"\n",file=fileout_FIDUCIARIA,append=T)
##        cat(paste(" DBunico: ",aux1),"\n",file=fileout_FIDUCIARIA,append=T)
##      }
##    } else {
##      if (DBmeteo2_Sensori$Fiduciaria[i]==aux1){
##       # print("Fiduciaria...OK")
##      } else {
##  cat("-----------------------------------------------------------------","\n",file=fileout_FIDUCIARIA,append=T)
##  cat(paste(i,". ID sensore =",DBmeteo2_Sensori$IDsensore[i],DBmeteo2_Sensori$NOMEtipologia[i],DBmeteo2_Sensori$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[DBmeteo2_Stazioni$IDstazione==DBmeteo2_Sensori$IDstazione[i]]),"\n",file=fileout_FIDUCIARIA,append=T)
  #      cat("@@@@@@@@@@@@@@@ Fiduciaria variato!","\n",file=fileout_FIDUCIARIA,append=T)
##        cat(paste("DBmeteo2: ",DBmeteo2_Sensori$Fiduciaria[i]),"\n",file=fileout_FIDUCIARIA,append=T)
##        cat(paste(" DBunico: ",aux1),"\n",file=fileout_FIDUCIARIA,append=T)
##      }
##    }
#
    if (DBunico_SensStaz$Pubblicabile[j]=='No') {
      aux1<-'No'
    } else {
      aux1<-'Yes'
    }
##    if ( is.na(DBmeteo2_Sensori$WEB[i]) | is.na(aux1) ) {
##      if ( is.na(DBmeteo2_Sensori$WEB[i]) & is.na(aux1) ) {
##       # print("WEB...OK")
##      } else {
##  cat("-----------------------------------------------------------------","\n",file=fileout_WEB,append=T)
##  cat(paste(i,". ID sensore =",DBmeteo2_Sensori$IDsensore[i],DBmeteo2_Sensori$NOMEtipologia[i],DBmeteo2_Sensori$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[DBmeteo2_Stazioni$IDstazione==DBmeteo2_Sensori$IDstazione[i]]),"\n",file=fileout_WEB,append=T)
##  #      cat("@@@@@@@@@@@@@@@ WEB variato!","\n",file=fileout_WEB,append=T)
##        cat(paste("DBmeteo2: ",DBmeteo2_Sensori$WEB[i]),"\n",file=fileout_WEB,append=T)
##        cat(paste(" DBunico: ",aux1),"\n",file=fileout_WEB,append=T)
##      }
##    } else {
##      if (DBmeteo2_Sensori$WEB[i]==aux1){
##       # print("WEB...OK")
##      } else {
##  cat("-----------------------------------------------------------------","\n",file=fileout_WEB,append=T)
##  cat(paste(i,". ID sensore =",DBmeteo2_Sensori$IDsensore[i],DBmeteo2_Sensori$NOMEtipologia[i],DBmeteo2_Sensori$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[DBmeteo2_Stazioni$IDstazione==DBmeteo2_Sensori$IDstazione[i]]),"\n",file=fileout_WEB,append=T)
##  #      cat("@@@@@@@@@@@@@@@ WEB variato!","\n",file=fileout_WEB,append=T)
##        cat(paste("DBmeteo2: ",DBmeteo2_Sensori$WEB[i]),"\n",file=fileout_WEB,append=T)
##        cat(paste(" DBunico: ",aux1),"\n",file=fileout_WEB,append=T)
##      }
##    }
#
    if (DBunico_SensStaz$Pubblicabile[j]=='No') {
      aux1<-'No'
    } else {
      aux1<-'Yes'
    }
    if ( is.na(DBmeteo2_Sensori$Google[i]) | is.na(aux1) ) {
      if ( is.na(DBmeteo2_Sensori$Google[i]) & is.na(aux1) ) {
       # print("Google...OK")
      } else {
  cat("-----------------------------------------------------------------","\n",file=fileout_GOOGLE,append=T)
  cat(paste(i,". ID sensore =",DBmeteo2_Sensori$IDsensore[i],DBmeteo2_Sensori$NOMEtipologia[i],DBmeteo2_Sensori$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[DBmeteo2_Stazioni$IDstazione==DBmeteo2_Sensori$IDstazione[i]]),"\n",file=fileout_GOOGLE,append=T)
  #      cat("@@@@@@@@@@@@@@@ Google variato!","\n",file=fileout_GOOGLE,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Sensori$Google[i]),"\n",file=fileout_GOOGLE,append=T)
        cat(paste(" DBunico: ",aux1),"\n",file=fileout_GOOGLE,append=T)
      }
    } else {
      if (DBmeteo2_Sensori$Google[i]==aux1){
       # print("Google...OK")
      } else {
  cat("-----------------------------------------------------------------","\n",file=fileout_GOOGLE,append=T)
  cat(paste(i,". ID sensore =",DBmeteo2_Sensori$IDsensore[i],DBmeteo2_Sensori$NOMEtipologia[i],DBmeteo2_Sensori$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[DBmeteo2_Stazioni$IDstazione==DBmeteo2_Sensori$IDstazione[i]]),"\n",file=fileout_GOOGLE,append=T)
  #      cat("@@@@@@@@@@@@@@@ Google variato!","\n",file=fileout_GOOGLE,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Sensori$Google[i]),"\n",file=fileout_GOOGLE,append=T)
        cat(paste(" DBunico: ",aux1),"\n",file=fileout_GOOGLE,append=T)
      }
    }
#
    if ( is.na(DBmeteo2_Sensori$AggregazioneTemporale[i]) | is.na(DBunico_SensStaz$FreqAcq[j]) ) {
      if ( is.na(DBmeteo2_Sensori$AggregazioneTemporale[i]) & is.na(DBunico_SensStaz$FreqAcq[j]) ) {
    #    print("AggregazioneTemporale...OK")
      } else {
  cat("-----------------------------------------------------------------","\n",file=fileout_AGG,append=T)
  cat(paste(i,". ID sensore =",DBmeteo2_Sensori$IDsensore[i],DBmeteo2_Sensori$NOMEtipologia[i],DBmeteo2_Sensori$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[DBmeteo2_Stazioni$IDstazione==DBmeteo2_Sensori$IDstazione[i]]),"\n",file=fileout_AGG,append=T)
  #      cat("@@@@@@@@@@@@@@@ AggregazioneTemporale variato!","\n",file=fileout_AGG,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Sensori$AggregazioneTemporale[i]),"\n",file=fileout_AGG,append=T)
        cat(paste(" DBunico: ",DBunico_SensStaz$FreqAcq[j]),"\n",file=fileout_AGG,append=T)
      }
    } else {
      if (DBmeteo2_Sensori$AggregazioneTemporale[i]==DBunico_SensStaz$FreqAcq[j]){
    #    print("AggregazioneTemporale...OK")
      } else {
  cat("-----------------------------------------------------------------","\n",file=fileout_AGG,append=T)
  cat(paste(i,". ID sensore =",DBmeteo2_Sensori$IDsensore[i],DBmeteo2_Sensori$NOMEtipologia[i],DBmeteo2_Sensori$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[DBmeteo2_Stazioni$IDstazione==DBmeteo2_Sensori$IDstazione[i]]),"\n",file=fileout_AGG,append=T)
  #      cat("@@@@@@@@@@@@@@@ AggregazioneTemporale variato!","\n",file=fileout_AGG,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Sensori$AggregazioneTemporale[i]),"\n",file=fileout_AGG,append=T)
        cat(paste(" DBunico: ",DBunico_SensStaz$FreqAcq[j]),"\n",file=fileout_AGG,append=T)
      }
    }
#
##### converto la data del DB unico in Date per togliere info relative a ore e minuti
     DBunico_SensStaz$DataMinimaHT[j] = as.Date(DBunico_SensStaz$DataMinimaHT[j])
    if ( is.na(DBmeteo2_Sensori$DataInizio[i]) | is.na(DBunico_SensStaz$DataMinimaHT[j]) ) {
      if ( is.na(DBmeteo2_Sensori$DataInizio[i]) & is.na(DBunico_SensStaz$DataMinimaHT[j]) ) {
    #    print("Data Inizio...OK")
      } else {
  cat("-----------------------------------------------------------------","\n",file=fileout_DATAI,append=T)
  cat(paste(i,". ID sensore =",DBmeteo2_Sensori$IDsensore[i],DBmeteo2_Sensori$NOMEtipologia[i],DBmeteo2_Sensori$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[DBmeteo2_Stazioni$IDstazione==DBmeteo2_Sensori$IDstazione[i]]),"\n",file=fileout_DATAI,append=T)
  #      cat("@@@@@@@@@@@@@@@ Data Inizio variato!","\n",file=fileout_DATAI,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Sensori$DataInizio[i]),"\n",file=fileout_DATAI,append=T)
        cat(paste(" DBunico: ",DBunico_SensStaz$DataMinimaHT[j]),"\n",file=fileout_DATAI,append=T)
      }
    } else {
      if (DBmeteo2_Sensori$DataInizio[i]==DBunico_SensStaz$DataMinimaHT[j]){
    #    print("Data Inizio...OK")
      } else {
  cat("-----------------------------------------------------------------","\n",file=fileout_DATAI,append=T)
  cat(paste(i,". ID sensore =",DBmeteo2_Sensori$IDsensore[i],DBmeteo2_Sensori$NOMEtipologia[i],DBmeteo2_Sensori$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[DBmeteo2_Stazioni$IDstazione==DBmeteo2_Sensori$IDstazione[i]]),"\n",file=fileout_DATAI,append=T)
  #      cat("@@@@@@@@@@@@@@@ Data Inizio variato!","\n",file=fileout_DATAI,append=T)
        cat(paste("DBmeteo2: ",DBmeteo2_Sensori$DataInizio[i]),"\n",file=fileout_DATAI,append=T)
        cat(paste(" DBunico: ",DBunico_SensStaz$DataMinimaHT[j]),"\n",file=fileout_DATAI,append=T)
      }
    }
#
#### converto la data del DB unico in Date per togliere info relative a ore e minuti
     DBunico_SensStaz$DataMassimaHT[j] = as.Date(DBunico_SensStaz$DataMassimaHT[j])
    if ( is.na(DBmeteo2_Sensori$DataFine[i]) | is.na(DBunico_SensStaz$DataMassimaHT[j]) ) {
      if ( is.na(DBmeteo2_Sensori$DataFine[i]) & is.na(DBunico_SensStaz$DataMassimaHT[j]) ) {
    #    print("Data Fine...OK")
      } else {
#  cat("-----------------------------------------------------------------","\n",file=fileout_DATAF,append=T)
#  cat(paste(i,". ID sensore =",DBmeteo2_Sensori$IDsensore[i],DBmeteo2_Sensori$NOMEtipologia[i],DBmeteo2_Sensori$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[DBmeteo2_Stazioni$IDstazione==DBmeteo2_Sensori$IDstazione[i]]),"\n",file=fileout_DATAF,append=T)
  #      cat("@@@@@@@@@@@@@@@ Data Fine variato!","\n",file=fileout_DATAF,append=T)
#        cat(paste("DBmeteo2: ",DBmeteo2_Sensori$DataFine[i]),"\n",file=fileout_DATAF,append=T)
#        cat(paste(" DBunico: ",DBunico_SensStaz$DataMassimaHT[j]),"\n",file=fileout_DATAF,append=T)
      }
    } else {
      if (DBmeteo2_Sensori$DataFine[i]==DBunico_SensStaz$DataMassimaHT[j]){
    #    print("Data Fine...OK")
      } else {
#  cat("-----------------------------------------------------------------","\n",file=fileout_DATAF,append=T)
#  cat(paste(i,". ID sensore =",DBmeteo2_Sensori$IDsensore[i],DBmeteo2_Sensori$NOMEtipologia[i],DBmeteo2_Sensori$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[DBmeteo2_Stazioni$IDstazione==DBmeteo2_Sensori$IDstazione[i]]),"\n",file=fileout_DATAF,append=T)
  #      cat("@@@@@@@@@@@@@@@ Data Fine variato!","\n",file=fileout_DATAF,append=T)
#        cat(paste("DBmeteo2: ",DBmeteo2_Sensori$DataFine[i]),"\n",file=fileout_DATAF,append=T)
#        cat(paste(" DBunico: ",DBunico_SensStaz$DataMassimaHT[j]),"\n",file=fileout_DATAF,append=T)
      }
    }


  }

  i<-i+1
}
#aux<-DBmeteo2_Stazioni$IDstazione %in% DBunico_Stazioni$IdStazione
#if (length(DBmeteo2_Stazioni$NOMEstazione[!aux])>0) {
#  print(DBmeteo2_Stazioni$NOMEstazione[!aux])
#} else {
#  print("Stazioni trovate 0")
#}

#------------------------------------------------------------------------------
close(DBunico_ch)
cat ( " \n\n\nChiusura connessione DBmeteo ed uscita dal programma con successo!!!","\n",file=fileout,append=T)
dbDisconnect(conn2)
rm(conn2)
dbUnloadDriver(drv)
warnings()
quit(status=0)

