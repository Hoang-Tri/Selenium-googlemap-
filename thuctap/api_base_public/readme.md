docker build -t demo_api_base_public .

docker run -d --restart always -v /root/dir_demo_api_base_public:/_app_/utils/download --name demo_api_base_public -p 55007:60074 demo_api_base_public

E:\Student\ThucTap\thuctap\DOCKER_DEMO

docker run -d --restart always -v e:/Student/ThucTap/thuctap/DOCKER_DEMO/demo/data_in:/_app_/demo/data_in --name demo_api_base_public -p 55007:60074 demo_api_base_public

E:\Student\ThucTap\thuctap\api_base_public\demo\data_in

docker save -o demo_api_base_public.tar demo_api_base_public

docker load -i demo_api_base_public.tar


docker exec -it demo_api_base_public bash
