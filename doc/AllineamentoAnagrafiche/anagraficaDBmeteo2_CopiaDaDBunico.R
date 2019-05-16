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
DBunico_SensStaz<- try( sqlQuery(DBunico_ch,paste("select Sensori.IdSensore,Sensori.IdStazione,Stazioni.idReteVis,Stazioni.NOME,Sensori.Storica,Stazioni.Pubblicabile,Sensori.IdTipologia,Sensori.FreqAcq,DataMinimaSensorePerRaggruppamento.DataMinimaHT,DataMinimaSensorePerRaggruppamento.DataMassimaHT from  Sensori,Stazioni,DataMinimaSensorePerRaggruppamento where Sensori.IdStazione=Stazioni.IdStazione and DataMinimaSensorePerRaggruppamento.IdSensore=Sensori.IdSensore and Sensori.IdTipologia in (2,3,5,9,10,11,12,13) and Stazioni.idReteVis in (1,2,4,5,6,7,8,9,10) order by Stazioni.NOME",sep="")))
DBunico_Web_SMR_Pubblicabili<- try( sqlQuery(DBunico_ch,paste("select Nome_WEB from Web_SMR_Pubblicabili",sep="")))
DBunico_Sensori<- try( sqlQuery(DBunico_ch,paste("select * from Sensori where IdTipologia in (2,3,5,9,10,11,12,13)",sep="")))
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
conn2<-try(dbConnect(drv,group="Visualizzazione2"))
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
# + Controlli di consistenza base
print("Ricerca stazioni appartenenti al DBunico E ad una rete di interesse E
       aventi almeno un sensore di interesse MA senza quota")
aux<-is.na(DBunico_Stazioni$Quota)
aux1<-DBunico_Stazioni$IdStazione[aux] %in% DBunico_SensStaz$IdStazione
print( cbind( as.vector(DBunico_Stazioni$Quota[aux][aux1]),
              as.vector(DBunico_Stazioni$Nome[aux][aux1]) ) )

print("Ricerca stazioni appartenenti al DBunico E ad una rete di interesse E
       aventi almeno un sensore di interesse MA senza CGB_Nord")
aux<-is.na(DBunico_Stazioni$CGB_Nord)
aux1<-DBunico_Stazioni$IdStazione[aux] %in% DBunico_SensStaz$IdStazione
print( cbind( as.vector(DBunico_Stazioni$CGB_Nord[aux][aux1]),
              as.vector(DBunico_Stazioni$Nome[aux][aux1]) ) )

print("Ricerca stazioni appartenenti al DBunico E ad una rete di interesse E
       aventi almeno un sensore di interesse MA senza CGB_Est")
aux<-is.na(DBunico_Stazioni$CGB_Est)
aux1<-DBunico_Stazioni$IdStazione[aux] %in% DBunico_SensStaz$IdStazione
print( cbind( as.vector(DBunico_Stazioni$CGB_Est[aux][aux1]),
              as.vector(DBunico_Stazioni$Nome[aux][aux1]) ) )

print("Ricerca stazioni appartenenti al DBunico E ad una rete di interesse E
       aventi almeno un sensore di interesse MA senza Nome")
aux<-is.na(DBunico_Stazioni$Nome)
aux1<-DBunico_Stazioni$IdStazione[aux] %in% DBunico_SensStaz$IdStazione
print( cbind( as.vector(DBunico_Stazioni$IdStazione[aux][aux1]),
              as.vector(DBunico_Stazioni$Nome[aux][aux1]) ) )

print("Ricerca stazioni appartenenti al DBunico E ad una rete di interesse E
       aventi almeno un sensore di interesse MA senza Provincia")
aux<-is.na(DBunico_Stazioni$Provincia)
aux1<-DBunico_Stazioni$IdStazione[aux] %in% DBunico_SensStaz$IdStazione
print( cbind( as.vector(DBunico_Stazioni$Provincia[aux][aux1]),
              as.vector(DBunico_Stazioni$Nome[aux][aux1]) ) )

print("Ricerca stazioni lombarde e appartenenti al DBunico E ad una rete di
       interesse E aventi almeno un sensore di interesse MA senza IDallerta")
aux<-is.na(DBunico_Stazioni$IdAllerta) & (DBunico_Stazioni$Provincia=="BG" |
     DBunico_Stazioni$Provincia=="BS" | DBunico_Stazioni$Provincia=="CO" |
     DBunico_Stazioni$Provincia=="CR" | DBunico_Stazioni$Provincia=="LC" |
     DBunico_Stazioni$Provincia=="LO" | DBunico_Stazioni$Provincia=="MB" |
     DBunico_Stazioni$Provincia=="MI" | DBunico_Stazioni$Provincia=="MN" |
     DBunico_Stazioni$Provincia=="PV" | DBunico_Stazioni$Provincia=="SO" |
     DBunico_Stazioni$Provincia=="VA")
aux1<-DBunico_Stazioni$IdStazione[aux] %in% DBunico_SensStaz$IdStazione
print( cbind( as.vector(DBunico_Stazioni$IdAllerta[aux][aux1]),
              as.vector(DBunico_Stazioni$Provincia[aux][aux1]),
              as.vector(DBunico_Stazioni$Nome[aux][aux1]) ) )

print("Ricerca stazioni appartenenti al DBunico  E ad una rete di interesse E
       aventi almeno un sensore di interesse MA non appartenenti al DBmeteo2")
aux<-!(DBunico_Stazioni$IdStazione %in% DBmeteo2_Stazioni$IDstazione)
aux1<-DBunico_Stazioni$IdStazione[aux] %in% DBunico_SensStaz$IdStazione
print( cbind( as.vector(DBunico_Stazioni$IdStazione[aux][aux1]),
              as.vector(DBunico_Stazioni$Provincia[aux][aux1]),
              as.vector(DBunico_Stazioni$Nome[aux][aux1]) ) )

print("Ricerca sensori appartenenti al DBunico  E ad una rete di interesse E
       di interesse MA non appartenenti al DBmeteo2")
aux<-DBunico_SensStaz$IdSensore %in% DBmeteo2_Sensori$IDsensore
if (length(DBunico_SensStaz$IdSensore[!aux])>0) {
  print(cbind(as.vector(DBunico_SensStaz$IdSensore[!aux]),
              as.vector(DBunico_SensStaz$IdStazione[!aux]),
              as.vector(DBunico_SensStaz$NOME[!aux]),
              as.vector(DBunico_SensStaz$idReteVis[!aux])  ) )
} else {
  print("Sensori trovate 0")
}

print("Ricerca stazioni appartenenti al DBmeteo2 ma non appartenenti al DBunico")
aux<-DBmeteo2_Stazioni$IDstazione %in% DBunico_Stazioni$IdStazione
if (length(DBmeteo2_Stazioni$NOMEstazione[!aux])>0) {
  print(DBmeteo2_Stazioni$NOMEstazione[!aux])
} else {
  print("Stazioni trovate 0")
}

print("Ricerca sensori appartenenti al DBmeteo2 ma non appartenenti al DBunico")
aux<-DBmeteo2_Sensori$IDsensore %in% DBunico_Sensori$IdSensore
if (length(DBmeteo2_Sensori$IDsensore[!aux])>0) {
  print(DBmeteo2_Sensori$IDsensore[!aux])
} else {
  print("Sensori trovate 0")
}
#------------------------------------------------------------------------------
# + Fra le info nel DBmeteo2: segnala parametri che variano rispetto al DBunico 
# Stazioni
print("Fra le info nel DBmeteo2: segnala parametri che variano rispetto al DBunico [Stazioni]")
i<-1
while(i<=length(DBmeteo2_Stazioni$IDstazione)) {
  print("-----------------------------------------------------------------")
  print(paste(i,". ID stazione =",DBmeteo2_Stazioni$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[i]))
  j<-which(DBunico_Stazioni$IdStazione==DBmeteo2_Stazioni$IDstazione[i])
  if (length(j)!=1) {
    print("l'ID stazione esiste nel DB METEO2 ma non nel DBunico")
    print(paste("ID stazione =",DBmeteo2_Stazioni$IDstazione[i]))
  } else {
#
    if ( is.na(DBmeteo2_Stazioni$NOMEstazione[i]) | is.na(DBunico_Stazioni$Nome[j]) ) {
      if ( is.na(DBmeteo2_Stazioni$NOMEstazione[i]) & is.na(DBunico_Stazioni$Nome[j]) ) {
        print("NOMEstazione...OK")
      } else {
        print("@@@@@@@@@@@@@@@ NOMEstazione variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Stazioni$NOMEstazione[i]))
        print(paste(" DBunico: ",DBunico_Stazioni$Nome[j]))
      }
    } else {
      if (DBmeteo2_Stazioni$NOMEstazione[i]==DBunico_Stazioni$Nome[j]){
        print("NOMEstazione...OK")
      } else {
        print("@@@@@@@@@@@@@@@ NOMEstazione variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Stazioni$NOMEstazione[i]))
        print(paste(" DBunico: ",DBunico_Stazioni$Nome[j]))
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
        print("CGB_Nord...OK")
      } else {
        print("@@@@@@@@@@@@@@@ CGB_Nord variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Stazioni$CGB_Nord[i]))
        print(paste(" DBunico: ",DBunico_Stazioni$CGB_Nord[j]))
      }
    } else {
      if (DBmeteo2_Stazioni$CGB_Nord[i]==DBunico_Stazioni$CGB_Nord[j]){
        print("CGB_Nord...OK")
      } else {
        print("@@@@@@@@@@@@@@@ CGB_Nord variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Stazioni$CGB_Nord[i]))
        print(paste(" DBunico: ",DBunico_Stazioni$CGB_Nord[j]))
      }
    }
#
    if ( is.na(DBmeteo2_Stazioni$CGB_Est[i]) | is.na(DBunico_Stazioni$CGB_Est[j]) ) {
      if ( is.na(DBmeteo2_Stazioni$CGB_Est[i]) & is.na(DBunico_Stazioni$CGB_Est[j]) ) {
        print("CGB_Est...OK")
      } else {
        print("@@@@@@@@@@@@@@@ CGB_Est variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Stazioni$CGB_Est[i]))
        print(paste(" DBunico: ",DBunico_Stazioni$CGB_Est[j]))
      }
    } else {
      if (DBmeteo2_Stazioni$CGB_Est[i]==DBunico_Stazioni$CGB_Est[j]){
        print("CGB_Est...OK")
      } else {
        print("@@@@@@@@@@@@@@@ CGB_Est variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Stazioni$CGB_Est[i]))
        print(paste(" DBunico: ",DBunico_Stazioni$CGB_Est[j]))
      }
    }
#
    if ( is.na(DBmeteo2_Stazioni$Quota[i]) | is.na(DBunico_Stazioni$Quota[j]) ) {
      if ( is.na(DBmeteo2_Stazioni$Quota[i]) & is.na(DBunico_Stazioni$Quota[j]) ) {
        print("Quota...OK")
      } else {
        print("@@@@@@@@@@@@@@@ Quota variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Stazioni$Quota[i]))
        print(paste(" DBunico: ",DBunico_Stazioni$Quota[j]))
      }
    } else {
      if (DBmeteo2_Stazioni$Quota[i]==DBunico_Stazioni$Quota[j]){
        print("Quota...OK")
      } else {
        print("@@@@@@@@@@@@@@@ Quota variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Stazioni$Quota[i]))
        print(paste(" DBunico: ",DBunico_Stazioni$Quota[j]))
      }
    }
#
    if ( is.na(DBmeteo2_Stazioni$IDrete[i]) | is.na(DBunico_Stazioni$idReteVis[j]) ) {
      if ( is.na(DBmeteo2_Stazioni$IDrete[i]) & is.na(DBunico_Stazioni$idReteVis[j]) ) {
        print("IDrete...OK")
      } else {
        print("@@@@@@@@@@@@@@@ IDrete variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Stazioni$IDrete[i]))
        print(paste(" DBunico: ",DBunico_Stazioni$idReteVis[j]))
      }
    } else {
      if (DBmeteo2_Stazioni$IDrete[i]==DBunico_Stazioni$idReteVis[j]){
        print("Quota...OK")
      } else {
        print("@@@@@@@@@@@@@@@ IDrete variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Stazioni$IDrete[i]))
        print(paste(" DBunico: ",DBunico_Stazioni$idReteVis[j]))
      }
    }
#
    if ( is.na(DBmeteo2_Stazioni$Provincia[i]) | is.na(DBunico_Stazioni$Provincia[j]) ) {
      if ( is.na(DBmeteo2_Stazioni$Provincia[i]) & is.na(DBunico_Stazioni$Provincia[j]) ) {
        print("Provincia...OK")
      } else {
        print("@@@@@@@@@@@@@@@ Provincia variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Stazioni$Provincia[i]))
        print(paste(" DBunico: ",DBunico_Stazioni$Provincia[j]))
      }
    } else {
      if (DBmeteo2_Stazioni$Provincia[i]==DBunico_Stazioni$Provincia[j]){
        print("Provincia..OK")
      } else {
        print("@@@@@@@@@@@@@@@ Provincia variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Stazioni$Provincia[i]))
        print(paste(" DBunico: ",DBunico_Stazioni$Provincia[j]))
      }
    }
#
    if ( is.na(DBmeteo2_Stazioni$Allerta[i]) | is.na(DBunico_Stazioni$IdAllerta[j]) ) {
      if ( is.na(DBmeteo2_Stazioni$Allerta[i]) & is.na(DBunico_Stazioni$IdAllerta[j]) ) {
        print("Allerta...OK")
      } else {
        print("@@@@@@@@@@@@@@@ Allerta variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Stazioni$Allerta[i]))
        print(paste(" DBunico: ",DBunico_Stazioni$IdAllerta[j]))
      }
    } else {
      if (DBmeteo2_Stazioni$Allerta[i]==DBunico_Stazioni$IdAllerta[j]){
        print("Allerta..OK")
      } else {
        print("@@@@@@@@@@@@@@@ Allerta variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Stazioni$Allerta[i]))
        print(paste(" DBunico: ",DBunico_Stazioni$IdAllerta[j]))
      }
    }
#
  }
  i<-i+1
} 
# Sensori
#DBunico_SensStaz<- try( sqlQuery(DBunico_ch,paste("select Sensori.IdSensore,Sensori.IdStazione,Stazioni.idReteVis,Stazioni.NOME from  Sensori,Stazioni where Sensori.IdStazione=Stazioni.IdStazione and Sensori.IdTipologia in (2,3,5,9,10,11,12,13) and Stazioni.idReteVis in (1,2,4,5,6,7,8,9,10) order by Stazioni.NOME",sep="")))
print("Fra le info nel DBmeteo2: segnala parametri che variano rispetto al DBunico [Sensori]")
i<-1
while(i<=length(DBmeteo2_Sensori$IDsensore)) {
  print("-----------------------------------------------------------------")
  print(paste(i,". ID sensore =",DBmeteo2_Sensori$IDsensore[i],DBmeteo2_Sensori$NOMEtipologia[i],DBmeteo2_Sensori$IDstazione[i],DBmeteo2_Stazioni$NOMEstazione[DBmeteo2_Stazioni$IDstazione==DBmeteo2_Sensori$IDstazione[i]]))
  j<-which(DBunico_SensStaz$IdSensore==DBmeteo2_Sensori$IDsensore[i])
  if (length(j)!=1) {
    print("l'ID sensore esiste nel DB METEO2 ma non nel DBunico")
    print(paste("ID sensore =",DBmeteo2_Sensori$IDsensore[i]))
  } else {
#
    if ( is.na(DBmeteo2_Sensori$IDstazione[i]) | is.na(DBunico_SensStaz$IdStazione[j]) ) {
      if ( is.na(DBmeteo2_Sensori$IDstazione[i]) & is.na(DBunico_SensStaz$IdStazione[j]) ) {
        print("IDstazione...OK")
      } else {
        print("@@@@@@@@@@@@@@@ IDstazione variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Sensori$IDstazione[i]))
        print(paste(" DBunico: ",DBunico_SensStaz$IdStazione[j]))
      }
    } else {
      if (DBmeteo2_Sensori$IDstazione[i]==DBunico_SensStaz$IdStazione[j]){
        print("IDstazione..OK")
      } else {
        print("@@@@@@@@@@@@@@@ IDstazione variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Sensori$IDstazione[i]))
        print(paste(" DBunico: ",DBunico_SensStaz$IdStazione[j]))
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
        print("IDtipologia...OK")
      } else {
        print("@@@@@@@@@@@@@@@ IDtipologia variato!")
        print(paste("DBmeteo2: ",aux))
        print(paste(" DBunico: ",DBunico_SensStaz$IdTipologia[j]))
      }
    } else {
      if (aux==DBunico_SensStaz$IdTipologia[j]){
        print("IDtipologia...OK")
      } else {
        print("@@@@@@@@@@@@@@@ IDtipologia variato!")
        print(paste("DBmeteo2: ",aux))
        print(paste(" DBunico: ",DBunico_SensStaz$IdTipologia[j]))
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
        print("Storico...OK")
      } else {
        print("@@@@@@@@@@@@@@@ Storico variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Sensori$Storico[i]))
        print(paste(" DBunico: ",aux1))
      }
    } else {
      if (DBmeteo2_Sensori$Storico[i]==aux1){
        print("Storico..OK")
      } else {
        print("@@@@@@@@@@@@@@@ Storico variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Sensori$Storico[i]))
        print(paste(" DBunico: ",aux1))
      }
    }
#
    aux<-grepl("\\*",toString(DBunico_SensStaz$NOME[j]))
    if (aux) {
      aux1<-'Yes'
    } else {
      aux1<-'No'
    }
    if ( is.na(DBmeteo2_Sensori$Fiduciaria[i]) | is.na(aux1) ) {
      if ( is.na(DBmeteo2_Sensori$Fiduciaria[i]) & is.na(aux1) ) {
        print("Fiduciaria...OK")
      } else {
        print("@@@@@@@@@@@@@@@ Fiduciaria variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Sensori$Fiduciaria[i]))
        print(paste(" DBunico: ",aux1))
      }
    } else {
      if (DBmeteo2_Sensori$Fiduciaria[i]==aux1){
        print("Fiduciaria...OK")
      } else {
        print("@@@@@@@@@@@@@@@ Fiduciaria variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Sensori$Fiduciaria[i]))
        print(paste(" DBunico: ",aux1))
      }
    }
#
    if (DBunico_SensStaz$Pubblicabile[j]=='No') {
      aux1<-'No'
    } else {
      aux1<-'Yes'
    }
    if ( is.na(DBmeteo2_Sensori$WEB[i]) | is.na(aux1) ) {
      if ( is.na(DBmeteo2_Sensori$WEB[i]) & is.na(aux1) ) {
        print("WEB...OK")
      } else {
        print("@@@@@@@@@@@@@@@ WEB variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Sensori$WEB[i]))
        print(paste(" DBunico: ",aux1))
      }
    } else {
      if (DBmeteo2_Sensori$WEB[i]==aux1){
        print("WEB...OK")
      } else {
        print("@@@@@@@@@@@@@@@ WEB variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Sensori$WEB[i]))
        print(paste(" DBunico: ",aux1))
      }
    }
#
    if (DBunico_SensStaz$Pubblicabile[j]=='No') {
      aux1<-'No'
    } else {
      aux1<-'Yes'
    }
    if ( is.na(DBmeteo2_Sensori$Google[i]) | is.na(aux1) ) {
      if ( is.na(DBmeteo2_Sensori$Google[i]) & is.na(aux1) ) {
        print("Google...OK")
      } else {
        print("@@@@@@@@@@@@@@@ Google variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Sensori$Google[i]))
        print(paste(" DBunico: ",aux1))
      }
    } else {
      if (DBmeteo2_Sensori$Google[i]==aux1){
        print("Google...OK")
      } else {
        print("@@@@@@@@@@@@@@@ Google variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Sensori$Google[i]))
        print(paste(" DBunico: ",aux1))
      }
    }
#
    if ( is.na(DBmeteo2_Sensori$AggregazioneTemporale[i]) | is.na(DBunico_SensStaz$FreqAcq[j]) ) {
      if ( is.na(DBmeteo2_Sensori$AggregazioneTemporale[i]) & is.na(DBunico_SensStaz$FreqAcq[j]) ) {
        print("AggregazioneTemporale...OK")
      } else {
        print("@@@@@@@@@@@@@@@ AggregazioneTemporale variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Sensori$AggregazioneTemporale[i]))
        print(paste(" DBunico: ",DBunico_SensStaz$FreqAcq[j]))
      }
    } else {
      if (DBmeteo2_Sensori$AggregazioneTemporale[i]==DBunico_SensStaz$FreqAcq[j]){
        print("AggregazioneTemporale...OK")
      } else {
        print("@@@@@@@@@@@@@@@ AggregazioneTemporale variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Sensori$AggregazioneTemporale[i]))
        print(paste(" DBunico: ",DBunico_SensStaz$FreqAcq[j]))
      }
    }
#
    if ( is.na(DBmeteo2_Sensori$DataInizio[i]) | is.na(DBunico_SensStaz$DataMinimaHT[j]) ) {
      if ( is.na(DBmeteo2_Sensori$DataInizio[i]) & is.na(DBunico_SensStaz$DataMinimaHT[j]) ) {
        print("Data Inizio...OK")
      } else {
        print("@@@@@@@@@@@@@@@ Data Inizio variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Sensori$DataInizio[i]))
        print(paste(" DBunico: ",DBunico_SensStaz$DataMinimaHT[j]))
      }
    } else {
      if (DBmeteo2_Sensori$DataInizio[i]==DBunico_SensStaz$DataMinimaHT[j]){
        print("Data Inizio...OK")
      } else {
        print("@@@@@@@@@@@@@@@ Data Inizio variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Sensori$DataInizio[i]))
        print(paste(" DBunico: ",DBunico_SensStaz$DataMinimaHT[j]))
      }
    }
#
    if ( is.na(DBmeteo2_Sensori$DataFine[i]) | is.na(DBunico_SensStaz$DataMassimaHT[j]) ) {
      if ( is.na(DBmeteo2_Sensori$DataFine[i]) & is.na(DBunico_SensStaz$DataMassimaHT[j]) ) {
        print("Data Fine...OK")
      } else {
        print("@@@@@@@@@@@@@@@ Data Fine variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Sensori$DataFine[i]))
        print(paste(" DBunico: ",DBunico_SensStaz$DataMassimaHT[j]))
      }
    } else {
      if (DBmeteo2_Sensori$DataFine[i]==DBunico_SensStaz$DataMassimaHT[j]){
        print("Data Fine...OK")
      } else {
        print("@@@@@@@@@@@@@@@ Data Fine variato!")
        print(paste("DBmeteo2: ",DBmeteo2_Sensori$DataFine[i]))
        print(paste(" DBunico: ",DBunico_SensStaz$DataMassimaHT[j]))
      }
    }


  }
#
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
print ( " Chiusura connessione DBmeteo ed uscita dal programma")
dbDisconnect(conn2)
rm(conn2)
dbUnloadDriver(drv)
warnings()
quit(status=0)

