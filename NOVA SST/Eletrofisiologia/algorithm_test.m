function [tempo_contract, tempo_relax] = algorithm_test(time, threshold, Data_tratada)
% Algorithm for detecting muscular contractions and relaxations in an EMG signal.
% Number of data points in the EMG signal
matriz_beg= zeros(4, 20);
matriz_end = zeros(4, 20);
tempo_contract = zeros(4,20);
tempo_relax = zeros(4,20);
c = 0;  % Flag variable for contraction detection

for t = 1:4
    data1 = Data_tratada(:,t);
    figure;  % Create a new figure
    plot(time, data1);  % Plot the EMG signal
    beginning_index(t)=0;
    end_index(t)=0;

hold on;
    for i = 2 : 40000
        if data1(i) >= threshold && data1(i-1) < threshold && c == 0
           plot(time(i), data1(i), 'og');  % Plot a green circle around the beginning of a contraction
           beginning_index(t) = beginning_index(t) + 1;
           matriz_beg(t, beginning_index(t)) = data1(i);
           tempo_contract(t, beginning_index(t)) = time(i);
           c = 1;  % Indicates the start of a contraction
        elseif data1(i) <= threshold && data1(i-1) > threshold && c == 1
           plot(time(i), data1(i), 'or');  % Plot a red circle around the beginning of a relaxation
           end_index(t) = end_index(t) + 1;
           matriz_end(t, end_index(t)) = data1(i);
           c = 0;  % Indicates the start of a relaxation phase
           tempo_relax(t, end_index(t)) = time(i);
        end
    end 
end 
hold off;



% Display the beginning and end matrix
disp(matriz_beg);
disp(matriz_end);
end




       
