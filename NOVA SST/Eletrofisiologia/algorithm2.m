function [tempo_contract, tempo_relax] = algorithm2(Data_tratada, time)

Min=mink(Data_tratada(:,3),round(length(time)/1.5));
emg_zero=Data_tratada(:,3);
threshold=mean(Min);

for i= 1:length(Data_tratada)
    if emg_zero(i)<=threshold
       emg_zero(i)=0;
    end
end

contraction=[];
relaxation=[];
tempo_relax=[];
tempo_contract=[];
setpoints=[];

for i= 2:length(Data_tratada)-1
    if emg_zero(i)>0
       contraction(end+1)=Data_tratada(i, 3);
    end
    if(emg_zero(i)==0)
       relaxation(end+1)=Data_tratada(i, 3);
    end
    if emg_zero(i) == 0 && emg_zero(i-1)>0
       tempo_relax(end+1)=time(i);
       setpoints(end+1)=Data_tratada(i, 3);
    end
    if emg_zero(i) == 0 && emg_zero(i+1)>0
       tempo_contract(end+1)=time(i);
       setpoints(end+1)=Data_tratada(i, 3);
    end
end
figure(5)
plot(time,Data_tratada(:,3))
xline(tempo_contract, '-g');
xline(tempo_relax, '-r');
xlabel("Tempo (s)");
ylabel('Tensão (mV)');
hold off


    Detetor = mean(setpoints)/mean(Data_tratada(:,3));
    if (Detetor>0.4)
        print('Doente');
    else
        print('Controlo');
    end


end
